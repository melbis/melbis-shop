// Vars
var melbis_topic_id = 0;

// Start
$( document ).ready(function() 
{
    melbis_navbar_active();    
});

// Navbar: set active menu item
function melbis_navbar_active()
{
    for( var item of $('.navbar-nav a')) 
    {   
        if ( $(item).data('id') == melbis_topic_id )
        {
            $(item).addClass('text-white');
            $(item).parent('li').addClass('bg-primary');
        }
    }
}

// Navbar: dropdown menu
$('.dropdown-menu').on('click', 'a.dropdown-toggle', function(event) 
{    
    if ( !$(this).next().hasClass('show') ) 
    {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
    }                                         
    var sub = $(this).next('.dropdown-menu');    
    if ( sub.children().length > 0 )
    {
        sub.toggleClass('show');
    }
    else
    {           
        sub.html('<li class="d-flex justify-content-center"><div class="spinner-border m-3"><span class="sr-only"></span></div></li>');
        sub.toggleClass('show');        
        $.post('/?mod=melbis_cataloge_sub',        
            { id: $(this).data('id') },
            function(data) 
            {            
               sub.html(data);               
               melbis_navbar_active();               
            }
        );
    }
    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) 
    {
        $('.dropdown-submenu .show').removeClass('show');
    });
    
    return false;
});


// Add to basket
$('.melbis_goods').on('click', '.melbis_btn_add', function(event)
{
    var name = $(this).parents('.card-body').find('h5').text();
    $.post('/?mod=melbis_basket',        
        { func: 'plus', id: $(this).data('id') },
        function(data) 
        {             
            $('#melbis_win_basket .modal-body').html('<p>Goods "' + name + '" added to basket!</p>');
            $('#melbis_win_basket').modal('show');                       
        }
    );    
});

// Remove from basket
$('#melbis_win_checkout').on('click', '.melbis_btn_remove', function(event)
{    
    $.post('/?mod=melbis_basket',        
        { func: 'minus', id: $(this).data('id') },
        function(data) 
        {                                     
            $('#melbis_order_goods').html(data);                           
        }
    );    
});

// Go to checkout
$('#melbis_btn_checkout').on('click', function(event)
{
    $('#melbis_win_basket').modal('hide');
    $('#melbis_win_checkout').modal('show');      
});

// Load checkout
$('#melbis_win_checkout').on('show.bs.modal', function (event) 
{
    $('.alert').hide();
    $('#melbis_order_fields').html('<li class="d-flex justify-content-center"><div class="spinner-border m-3"><span class="sr-only"></span></div></li>');
    $('#melbis_order_goods').html('<li class="d-flex justify-content-center"><div class="spinner-border m-3"><span class="sr-only"></span></div></li>');
    $('#melbis_order_options').html('<li class="d-flex justify-content-center"><div class="spinner-border m-3"><span class="sr-only"></span></div></li>');
    $.post('/?mod=melbis_basket', { func: 'fields' }, function(data) { $('#melbis_order_fields').html(data); } );
    $.post('/?mod=melbis_basket', { func: 'goods' }, function(data) { $('#melbis_order_goods').html(data); } );
    $.post('/?mod=melbis_basket', { func: 'options' }, function(data) { $('#melbis_order_options').html(data); } );       
});


// Finish
$('#melbis_btn_finish').on('click', function(event)
{
    for( var item of $('#melbis_form_order select') )
    { 
        $(item).prev('input').val($('option:selected', $(item)).text());
    }
        
    var form = $('#melbis_form_order').melbis_serial();
    form.func = 'save';
    $.post('/?mod=melbis_basket', form, function(data) 
        {                     
            if ( data.result != 'OK' )
            {
                $('.alert span').html('[' + data.result + '] ' + data.message);
                $('.alert').addClass('show').show();        
            }
            else
            {
                $('#melbis_win_checkout').modal('hide');    
                $('#melbis_win_finish').modal('show');
            }         
        }, 
        'json');                
});


// Serialize Object
$.fn.melbis_serial = function()
{
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });   
   
   return o;
};


// Numbers round and format
function melbis_num(n, c)
{
    var n = n, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "'" : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


