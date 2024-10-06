const type_select = document.querySelector("#type");
const short_text = document.querySelector("#short-text");
const textarea = document.querySelector("#textarea");

type_select.addEventListener("change", () => {
    if (type_select.value === "short") {
        short_text.classList.remove("hidden");
        textarea.ariaRequired = "true";
    } else {
        short_text.classList.add("hidden");
        textarea.ariaRequired = "false";
    }
});
