//Opciones para dar formato a la fecha
var options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
};

const direccion_API = "http://www.dircultura.tst/api/cal/actividades";

//Se agrega el calendario
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'local',
        locale: 'es',
        plugins: ['interaction', 'dayGrid'],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        selectable: true,
        navLinks: true,
        eventLimit: false,
        eventClick: muestraModal,
        events: direccion_API
    });

    calendar.render();
});

//Funcion llamada al hacer click a una actividad (event)
function muestraModal(info) {
    //edita propiedades segun el color del evento
    $('.modal-header').css("background-color", info.event.backgroundColor);
    $('.hr-modal').css("background-color", info.event.backgroundColor);
    $('.modal-footer').css("border-top-color", info.event.backgroundColor);

    //Campos que siempre van a estar
    //Titulo
    $('#title-modal').html(info.event.title);
    //Categoria
    $('#modal-categoria').html(info.event.extendedProps.category);
    //Descripcion
    $('#desc-modal').html(info.event.extendedProps.desc);
    //Fecha de inicio
    var fechaMostrar = '';
    var fechaInicio = info.event.start.toLocaleDateString("es-ES", options);
    var fechaMostrar = fechaInicio.charAt(0).toUpperCase() + fechaInicio.slice(1);


    //Campos que pueden ser nulos por lo que se valida
    //Encargado
    if (info.event.extendedProps.inCharge) {
        $('#encargado-modal').html(info.event.extendedProps.inCharge);
        $('#encargado-modal-div').css("display", 'inline');
    } else {
        $('#encargado-modal-div').css("display", 'none');
    }
    //Fecha final
    if (info.event.end) {
        var fechaFin = info.event.end.toLocaleDateString("es-ES", options);
        fechaMostrar = 'Del ' + fechaMostrar + ' al ' + fechaFin;
    }
    //Hora
    if (!info.event.allDay) {
        var hora = info.event.start;
        hora = hora.toString().substr(16, 5);
        fechaMostrar += ' a las ' + hora;
    }
    //Contacto
    if (info.event.extendedProps.contact) {
        $('#contacto-modal').html(info.event.extendedProps.contact);
        $('#contacto-modal-div').css("display", 'inline');
    } else {
        $('#contacto-modal-div').css("display", 'none');
    }
    //Cancelado
    if (info.event.extendedProps.canceled == 1) {
        $('#cancelado-modal').html(info.event.extendedProps.reason);
        $('#modal-canceled-div').css("display", 'inline');
    } else {
        $('#modal-canceled-div').css("display", 'none');
    }
    //Boton a Face
    if (info.event.extendedProps.link_fb) {
        $('#faceb-modal').attr("href", info.event.extendedProps.link_fb);
        $('#modal-facebook-div').css("display", 'inline');
    } else {
        $('#modal-facebook-div').css("display", 'none');
    }
    //Mostrar fecha
    $('#fecha-modal').html(fechaMostrar);

    //muestra el modal
    $('.modal').modal('show');
}