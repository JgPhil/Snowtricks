var block = document.getElementById("block-figures");
var config = {
    childList: true,
    subtree: true
}

var callback = function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type == 'subtree' || mutation.type == 'childList') {

            if (block.firstElementChild.childElementCount >= 10) {
                alert("WAKI!!! WAKI!!!")
            }
        }
    }
}

var observer = new MutationObserver(callback);
observer.observe(block, config);

observer.disconnect();