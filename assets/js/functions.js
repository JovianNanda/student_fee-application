function isInvalid(target, message) {
  if(!target || target == null) return false
  const tag = document.querySelector(target)
  const node = document.createElement("div")
  let parent = tag.parentElement;
  tag.classList.add("is-invalid");

  if (parent.classList.contains("input-icon")) {
    parent = parent.parentElement;
    tag.style.backgroundImage = "url('')";
  }

  parent.append(node);
  node.innerText = message;
  node.classList.add("invalid-feedback");
}

function setValue(target, message) {
  const tag = document.querySelector(target);
  tag.value = message;
}

function hideOnClickOutside(element) {
  const outsideClickListener = (event) => {
    if (!element.contains(event.target)) {
      parent = element.parentElement;
      parent.classList.remove("show");
    }
  };

  document.addEventListener("click", outsideClickListener);
}

// Alert dismiss logic
const alert = document.querySelectorAll(".alert");
const floatingAlert = document.querySelectorAll(".alert.floating-alert");

alert.forEach(function (alert) {
  setTimeout(() => {
    alert.classList.add("show");
  }, 150);
  const button = alert.querySelector("button[alert-dismiss]");
  button.addEventListener("click", () => {
    alert.style.opacity = "0";
    setTimeout(() => {
      alert.remove();
    }, 250);
  });

  floatingAlert.forEach(function (flAlert) {
    setTimeout(() => {
      flAlert.classList.remove("show");
    }, 2500);
  });
});

// Modal dismiss logic
const confirmModal = document.querySelectorAll(".modal");
const body = document.querySelector("body");

confirmModal.forEach(function (modal) {
  const modalContent = modal.children[0];

  const exitModal = modal.querySelector("button[modal-dismiss]");
  exitModal.addEventListener("click", () => {
    if (modal.classList.contains("show")) {
      modalContent.style.transform = "scale(0)";
      modal.style.opacity = "0";

      setTimeout(() => {
        modal.classList.remove("show");
        body.classList.remove("overflow-hidden");
        modal.style.display = "none";
      }, 150);
    }
  });
});

// modal logic
const btnTarget = document.querySelectorAll("[modal-target]");
const id = document.querySelectorAll("[modal-id]");

btnTarget.forEach(function (target) {
  let attribute = target.getAttribute("modal-target");

  target.addEventListener("click", () => {
    id.forEach(function (id) {
      const targetChild = id.children[0];
      let targetId = id.getAttribute("modal-id");

      if (attribute == targetId) {
        if (!id.classList.contains("show")) {
          id.style.display = "flex";
          id.style.transform = "scale(1)";
          id.style.opacity = "1";

          setTimeout(() => {
            body.classList.add("overflow-hidden");
            targetChild.style.transform = "scale(1)";
            id.classList.add("show");
          }, 150);
        } else {
          id.style.display = "none";
          id.style.transform = "scale(0)";
          id.style.opacity = "0";

          setTimeout(() => {
            targetChild.style.transform = "scale(0)";
            id.classList.remove("show");
          }, 150);
        }
      }
    });
  });
});

// data toggle dropdown
const btnToggle = document.querySelectorAll("[data-toggle='dropdown']");

btnToggle.forEach((btn) => {
  const dropdownParent = btn.parentElement;
  const dropdownMenu = dropdownParent.querySelector(".dropdown-menu");
  btn.addEventListener("click", (e) => {
    dropdownParent.classList.toggle("show");
    setTimeout(function () {
      hideOnClickOutside(btn);
    }, 250);
  });
});

// data toggle password must have input-icon
const btnTogglePassword = document.querySelectorAll("[data-toggle='password']");
btnTogglePassword.forEach((btn) => {
  const inputPassword = btn.parentElement.children[0];
  btn.style.display = "none";

  // Cek awal
  if (inputPassword.value != "") {
    btn.style.display = "block";
  } else {
    btn.style.display = "none";
  }

  // cek saat merubah
  inputPassword.addEventListener("keyup", () => {
    if (inputPassword.value != "") {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  });

  const iconBtn = document.querySelectorAll(".button.icon");

  iconBtn.forEach(function (ico) {
    const icon = ico.children[0];
    btn.addEventListener("click", () => {
      if (inputPassword.getAttribute("type") == "password") {
        inputPassword.setAttribute("type", "text");
        icon.classList.remove("ico-eye");
        icon.classList.add("ico-eye-slash");
      } else if (inputPassword.getAttribute("type") == "text") {
        inputPassword.setAttribute("type", "password");
        icon.classList.add("ico-eye");
        icon.classList.remove("ico-eye-slash");
      }
    });
  });
});

// Change Tab
const tab = document.querySelector("section" + window.location.hash);
const activeSession = document.querySelector("section.active");
if (tab && activeSession) {
  const sectionActive = document.querySelector("section.active").getAttribute("id");
  tab.classList.toggle("active");
  tab.classList.toggle("d-none");
  activeSession.classList.toggle("d-none");
  activeSession.classList.toggle("active");
  const btnNav = document.querySelectorAll(".navigation:not(.active)");
  if (btnNav) {
    btnNav.forEach((btn) => {
        if(window.location.hash){
          const navActive = document.querySelector(".navigation[data-target='"+window.location.hash+"']")

          navActive.classList.add("active")
        }else{
          const navActive = document.querySelector(".navigation[data-target='#"+sectionActive+"']")

          navActive.classList.add("active")
        }

        btn.addEventListener("click", () => {
        const btnNavActive = document.querySelectorAll(".navigation.active")
        btnNavActive.forEach( (btnActive) => {
          btnActive.classList.remove("active")
        })
        const target = btn.getAttribute("data-target");
        btn.classList.toggle("active")
        window.location.hash = target;
        const tab = document.querySelector("section" + target);
        const activeSession = document.querySelector("section.active");
        tab.classList.toggle("active");
        tab.classList.toggle("d-none");
        activeSession.classList.toggle("d-none");
        activeSession.classList.toggle("active");
      });
    });
  }
}
