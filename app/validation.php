<?php
function isGuest()
{
    return !isset($_SESSION['login']);
}

function isLogin()
{
    return isset($_SESSION['login']);
}

function redirect($redirect)
{
    header("location:" . homeUrl() . "/$redirect");
    exit();
}

function getLevel()
{
    return (isLogin()) ? $_SESSION['login']['auth'] : null;
}

function getUName()
{
    return (isLogin()) ? $_SESSION['login']['nama'] : null;
}

function getUID()
{
    return (isLogin()) ? $_SESSION['login']['uid'] : null;
}

function isSiswa()
{
    return getLevel() == "siswa";
}

function isPetugas()
{
    return getLevel() == "petugas";
}

function isAdmin()
{
    return getLevel() == "admin";
}

function isPost()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function isEmail($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function sanitize($text)
{
    if (is_string($text)) {
        return filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS);
    } else if (is_numeric($text)) {
        if (is_integer($text)) {
            return filter_var($text, FILTER_SANITIZE_NUMBER_INT);
        } else if (is_float($text)) {
            return filter_var($text, FILTER_SANITIZE_NUMBER_FLOAT);
        }
    }
    return $text;
}


/**
 *
 * looping rulenya , dpetin keynya atau fieldnya dan rulenya
 * call user func , berdasarkan rule, siapin variable error yg
 * semua field entah isinya error atau tida
 */
$errors = [];
function validate($data, $validate, $uniqueTable = null)
{
    global $errors;
    foreach ($validate as $attribute => $rules) {
        $value = $data[$attribute] ?? false;
        $oldValue[] = $value;
        $oldAttr[] = "#" . $attribute;
        foreach ($rules as $rule) {
            $ruleName = $rule;
            if (!is_string($ruleName)) {
                $ruleName = $rule[0];
            }
            if ($ruleName === RULE_REQUIRED && !$value) {
                $errors[] = addError($attribute, RULE_REQUIRED, $rule);
            }
            if ($ruleName === RULE_NUM && !is_numeric($value)) {
                $errors[] = addError($attribute, RULE_NUM, $rule);
            }
            if ($ruleName === RULE_EMAIL && !isEmail($value)) {
                $errors[] = addError($attribute, RULE_EMAIL, $rule);
            }
            if ($ruleName === RULE_MIN && ($value) ? strlen($value) < $rule['min'] : "") {
                $errors[] = addError($attribute, RULE_MIN, $rule);
            }
            if ($ruleName === RULE_MAX && ($value) ? strlen($value) > $rule['max'] : "") {
                $errors[] = addError($attribute, RULE_MAX, $rule);
            }
            if ($ruleName === RULE_UNIQUE) {
                if ($value) {
                    $tableName = $rule['table'] ?? $uniqueTable;
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $record = query("SELECT * FROM $tableName WHERE $uniqueAttr = '$value'");
                    if ($record) {
                        $errors[] = addError($attribute, RULE_UNIQUE, $rule);
                    }
                }
            }

            if ($value) {
                setValue($oldAttr, $oldValue);
            }

        }

    }

    if ($errors) {
        foreach ($errors as $error) {
            $errAttr[] = $error[0];
            $errMessage[] = $error[1];
        }
        $errAttr = array_unique($errAttr);
        setIsInvalid($errAttr, $errMessage);
    }

    if (!empty($errMessage) or !empty($errAttr)) {
        return false;
    }
}

function addError($attribute, $rule, $params = [])
{
    $message = errorMessages()[$rule] ?? '';
    if (is_array($params)) {
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
            $message = str_replace("_", " ", $message);
        }
    } else {
        $message = str_replace("{{$rule}}", ucfirst($attribute), $message);
        $message = str_replace("_", " ", $message);
    }

    return [$err[0], $err[1]] = ["#" . $attribute, $message];
}

function errorMessages()
{
    return [
        RULE_REQUIRED => '{required} wajib diisi',
        RULE_EMAIL => 'Field {email} wajib berisi alamat email yang valid',
        RULE_MIN => 'Panjang minimal dari field ini adalah {min}',
        RULE_MAX => 'Panjang maximal dari field ini adalah {max}',
        RULE_NUM => 'Field {num} Harus berisi Nomor',
        RULE_UNIQUE => '{unique} Sudah Terdaftar!',
    ];
}

function isErrors()
{
    global $errors;
    return !empty($errors);
}
