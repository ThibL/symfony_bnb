$('#add-image').click(function() {
    //récupération du numéro des champs
    const index = +$('#widgets-counter').val();
    //récupération du prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);
    //j'injecte le code au sein de la div
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // je gère le bouton supprimer
    handleDeleteButton();
});

function handleDeleteButton() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter()

handleDeleteButton()