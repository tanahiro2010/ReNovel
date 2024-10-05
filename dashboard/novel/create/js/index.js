const type_select = document.querySelector("#type");
const short_text = document.querySelector("#short-text");
const textarea = document.querySelector("#textarea");

type_select.addEventListener("change", () => {
    if (type_select.value === "short") {
        short_text.style = "";
        textarea.ariaRequired = "true";
    } else {
        short_text.style = "display: none";
        textarea.ariaRequired = "false";
    }
});
