const tour = new Shepherd.Tour({
    useModalOverlay: true,
    defaultStepOptions: {
        cancelIcon: {
            enabled: true
        },
        classes: 'class-1 class-2',
        scrollTo: { behavior: 'smooth', block: 'center' }
    }
});
$(".startTour").on("click", function () {
    elements = $('#elementTour').val().split(',');
    $.ajax({
        url: "v3/utilidades/leerTour",
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('#token').val()
        },
        data: {
            controlador: $('#controlador').val(),
            elements: elements
        },
        dataType: 'json',
        success: function (json) {
            console.log(json);
            for (var i = 0; i < json.length; i++) {
                tour.removeStep(json[i][2].element.replaceAll(' ', '').trim());
            }
            for (var i = 0; i < json.length; i++) {
                if (i == 0) {
                    tour.addStep(
                        {
                            title: json[i][0].title,
                            text: json[i][1].text,
                            attachTo: {
                                element: '.' + json[i][2].element.replaceAll(' ', '').trim(),
                                on: json[i][3].on.replaceAll(' ', '').trim()
                            },
                            buttons: [
                                {
                                    action() {
                                        return this.next();
                                    },
                                    text: $('#next').val()
                                },
                                {
                                    action() {
                                        return this.complete();
                                    },
                                    classPrefix: 'shepherd-button-secondary-danger',
                                    text: 'End'
                                }
                            ],
                            id: 'creating'
                        }
                    );
                } else if (i == (json.length - 1)) {
                    tour.addStep(
                        {
                            title: json[i][0].title,
                            text: json[i][1].text,
                            attachTo: {
                                element: '.' + json[i][2].element.replaceAll(' ', '').trim(),
                                on: json[i][3].on.replaceAll(' ', '').trim()
                            },
                            buttons: [
                                {
                                    action() {
                                        return this.back();
                                    },
                                    classes: 'shepherd-button-secondary',
                                    text: $('#back').val()
                                },
                                {
                                    action() {
                                        return this.complete();
                                    },
                                    classes: 'shepherd-button-secondary-danger',
                                    text: 'End'
                                }
                            ],
                            id: 'creating'
                        }
                    );
                } else {
                    tour.addStep(
                        {
                            title: json[i][0].title,
                            text: json[i][1].text,
                            attachTo: {
                                element: '.' + json[i][2].element.replaceAll(' ', '').trim(),
                                on: json[i][3].on.replaceAll(' ', '').trim()
                            },
                            buttons: [{
                                action() {
                                    return this.back();
                                },
                                classes: 'shepherd-button-secondary',
                                text: $('#back').val()
                            },
                            {
                                action() {
                                    return this.next();
                                },
                                text: $('#next').val()
                            },
                            {
                                action() {
                                    return this.complete();
                                },
                                classes: 'shadow-md bg-purple-dark',
                                text: 'End'
                            }
                            ],
                            id: 'creating'
                        }
                    );
                }
            }
            tour.start();
        }
    });
});