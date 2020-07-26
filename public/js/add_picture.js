


jQuery(document).ready(function () {
    jQuery('.add-another-picture-widget').click(function (e) {
        let pictureList = jQuery(jQuery(this).attr('data-list'));
        // Try to find the counter of the list or use the length of the list
        let counter = pictureList.data('widget-counter') | pictureList.children().length;
        // grab the prototype template
        let newWidget = pictureList.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        pictureList.data('widget-counter', counter);
        // create a new list element and add it to the list
        let pictureNewElem = jQuery(pictureList.attr('data-widget-tags')).html(newWidget);

        //create a button to close the li element
        let closeLi = document.createElement('a');
        closeLi.classList.add("closeLi");
        closeLi.innerHTML = "X";

        pictureNewElem.append(closeLi);
        pictureNewElem.appendTo(pictureList);
    });
});

var picturelistElement = document.querySelector("#pictureList-fields-list");
if (picturelistElement) {
    picturelistElement.addEventListener("click", function (e) {
        if (e.target && e.target.matches('a')) {
            e.preventDefault();
            e.stopPropagation();
            e.target.parentElement.remove();
        }
    });
}



