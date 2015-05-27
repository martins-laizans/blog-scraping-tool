
$('#myModal').on('hidden', function () {
        $('.TableMenu').hide();
});

$('.TableMenu').hide();
$('#loading').hide();
$('.toolTip2').tooltip();

//hover effect for month day's
$('.dateCell').hover(function(){
        $(this).addClass('dayHover');
    }, 
    function(){
        $(this).removeClass('dayHover');
});

//for buttons to stay in place when browser window is resized
$(window).resize(function() {
    positionButtons();
});

//set previous and next buttons to sides of calendar and in middle of calendars height
function positionButtons(){
    var tablePosition = $('#calendarTable').position();
    var tableHeight = $('#calendarTable').height();
    var tableWidth = $('#calendarTable').width();
    $('#goToPreviousMonth').css({'left' : tablePosition.left-80, 'top' : (tablePosition.top + tableHeight/2)});
    $('#goToNextMonth').css({'left' : (tablePosition.left + tableWidth + 80), 'top' : (tablePosition.top + tableHeight/2)});
    $('#loading').css({'left' : (tablePosition.left + tableWidth/2), 'top' : (tablePosition.top + tableHeight/2)});
}

positionButtons();

// load data about clicked day into modal window and show it 
$('.dateCell').click(function(){
    $('.greyBackground').removeClass('greyBackground');
    $('.TableMenu').hide();

    if ($(this).hasClass('otherMonthDay')) {
        if ($(this).hasClass('nextMonth'))
            window.location.href = nextMonthUrl;
        else
            window.location.href = previousMonthUrl;
        return false;}
    else
    {
        $(this).addClass('greyBackground'); //workaround for day menu width not being 100%
        
        var tdPosition = $(this).position();
        if($(this).hasClass('emtpy')){
            $('#dayEmptyMenu').css({'left' : tdPosition.left+1, 'top' : (tdPosition.top+1)}); //.html('<p>Empty day</p>');
            $('#dayMenu').fadeOut();
            $('#pickedDate').removeClass().addClass($(this).attr('id'));
            var dateClass = $(this).attr('id');
            dateClass = dateClass.replace(/\//g, '_');
            dateClass = 'd_'+dateClass;
            $('#pickedDateForMenu').removeClass().addClass(dateClass);
            
            $('#dayEmptyMenu').fadeIn(fadeInDuration);
        }
        else
            if(($(this).hasClass('hasEvents')))
        {
            $('#dayMenu').css({'left' : tdPosition.left+1, 'top' : (tdPosition.top+1)});
            $('#dayEmptyMenu').fadeOut();
            $('#pickedDate').removeClass().addClass($(this).attr('id'));
            $('#dayMenu').fadeIn(fadeInDuration);
            var dateClass = $(this).attr('id');
            dateClass = dateClass.replace(/\//g, '_');
            dateClass = 'd_'+dateClass;
            $('#pickedDateForMenu').removeClass().addClass(dateClass);

            $('#eventModalBody').html($(this).children('div :first').children('div :nth-child(2)').html());
            var title = $(this).attr('id');
            title = title.replace(/\//g,"-"); 
            $('#myModalLabel').html('Notikumu saraksts ' + title);
        }
//        alert($('#pickedDateForMenu').attr('class'));
    }
});

//load event create form via ajax call and fill in the date of clicked cell
function loadCreateEventForm()
{
    $('.hideFromAddEvent').hide();
    var request = $.ajax({
        url: createEventUrl,
        type: "POST",
        data: {date :  $('#pickedDate').attr('class')},
        dataType: "html",
        beforeSend: function () {
            $('#loading').fadeIn(100);
        }
    });
    request.done(function(result) {
        $('#loading').hide();
        $("#myModalLabel").html('Pievienot notikumu');
        $("#eventModalBody").html(result);
        $('#additional').hide();
        $('#myModal').modal();
        $("#additional").hide();
        $("#show_additional").click(function(){
            $("#additional").toggle();
            if ($(this).text() == 'hide additional')
                $(this).text(showAdditional);
            else
                $(this).text(hideAdditional);
            return false;
        });
        $('#form_submit').click(function(){
            var eventType = $('#form_event_type').find('option:selected').val();
            if(eventType == 'Izvēlies notikuma veidu')
            {
                alert('must choose event type');
                return false;
            }
        });
    });
    request.fail(function(jqXHR, textStatus) {
         loadCreateEventForm();
    });
}
$('.createEvent').click(function(){
    loadCreateEventForm();
});

$('#myModal').on('hidden', function () {
    $('.greyBackground').removeClass('greyBackground');
});

//load modal window with list of this day's events
$('.daysEventList').click(function(){
    $('.hideFromAddEvent').show();
    $('#myModal').modal();
});

/*
    //  Show hide events from calendar view
*/

//set all check boxes to show on page load
$('#event-selector').find(':checkbox').prop('checked', 'checked');

//get icon of checkbox checked and toggle same icons in calendar
$('.checkbox ').change(function(){
        var text = $(this).html();
        var index = text.indexOf('icon');
        var end = text.indexOf('"></i>');
        var output = text.substr(index,end-index);
        $('table .'+output).toggle();
    });

// (un)check all ( solution idea taken from http://briancray.com/posts/check-all-jquery-javascript )
$('#inlineCheckboxAll').click(function(){
    if($(this).prop('checked'))
        {
            $(this).parents('#event-selector').find(':checkbox').prop('checked', 'checked');
            $('table i').show();
            $('table .namesDay').show();
        }
    else
        {
            $(this).parents('#event-selector').find(':checkbox').attr('checked', this.checked);
            $('table i').hide();
            $('table .namesDay').hide();
        }
});

//(un)check namesdays
$('#inlineCheckboxNamesdays').click(function(){
    if($(this).prop('checked'))
        {
            $('table .namesDay').show();
        }
    else
        {
            $('table .namesDay').hide();
        }
});

/*
    //  Mark day as special
*/

//show special day mark options in modal
$('.markAsSpecial').click(function(){
    $('#specialModal').modal();
});

function markSpecialAjax()
{
    var selectedStyle = $('input[type=radio]:checked').val();
    var test = '.'+$('#pickedDateForMenu').attr('class');
    
    //send selected type via ajax
    var request = $.ajax({
        url: markAsSpecialUrl,
        type: "POST",
        data: {date :  $('#pickedDate').attr('class'), borderId : selectedStyle},
        dataType: "html",
        beforeSend: function () {
            $('#loading').fadeIn(100);
            //alert('seding data: '+$('#pickedDate').attr('class')+' '+selectedStyle)
        }
    });
    request.done(function(result) {
        $('#loading').hide();
        for (var i =1; i<6; i++)
        {
            $(test).removeClass('special'+i);
        }
        $(test).addClass('specialDay').addClass('special'+selectedStyle);
        $('.TableMenu').hide();
        $('#specialModal').modal('hide');
        $('.greyBackground').removeClass('greyBackground');
        //alert(result);
    });
    request.fail(function(jqXHR, textStatus, rest) {
        markSpecialAjax();
//        alert( "Request failed: " + textStatus + jqXHR + rest);
    });
}

//mark day as special, show day decoration and via ajax call save changes
$('#markSpecial').click(function(){
    markSpecialAjax();
});

    
$('#month_dropdown').change(function(){
    var month = $(this).val();
   window.location = baseUrl+'event/month_calendar/'+loadYear+'/'+month;
});

$('#year_dropdown').change(function(){
    var year = $(this).val();
    window.location = baseUrl+'event/month_calendar/'+year+'/'+loadMonth;
});