/*       Melbis Shop auto bundle report       */
/*         Create: 2026-07-17 15:39:56        */

/*   #1    main.js                         32 ln     1 kb    /templates/default/statics/melbis/main.js                     */
/*   #10   scripts.js                      57 ln     1 kb    /templates/default/units/melbis_cataloge/scripts.js           */
/*   #15   scripts.js                      77 ln     3 kb    /templates/default/units/melbis_base_page/scripts.js          */


/***************************************************************************************************
 * @version 6.5.0.335 @ 2026-07-23
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov    
 **************************************************************************************************/
         
// Serialize Object
$.fn.melbis_serial = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() 
    {
        if (o[this.name]) 
        {
            if (!o[this.name].push) 
            {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } 
        else 
        {
            o[this.name] = this.value || '';
        }
    });   
   
   return o;
};



// Navbar
//-------

// Start
$(document).ready(function() 
{
    melbis_navbar_active();    
});

// Set active menu item
function melbis_navbar_active()
{    
    for ( var item of $('.navbar-nav a') ) 
    {   
        if ( $(item).data('id') == melbis_topic_id )
        {
            $(item).addClass('text-white');
            $(item).parent('li').addClass('bg-primary');
        }
    }
}

// Dropdown menu
$('.dropdown-menu').on('click', 'a.dropdown-toggle', function(event) 
{        
    event.preventDefault();
    
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



// Basket
//-------

// Add to basket
$('.melbis_goods').on('click', '.melbis_btn_add', function(event)
{
    var name = $(this).parents('.card').find('h4').text();
    $.post('/?mod=melbis_basket',        
        { func: 'plus', id: $(this).data('id') },
        function(data) 
        {             
            $('#melbis_win_basket .modal-body').html('<p>' + name + '</p><p>Product has been successfully added to your cart!</p>');
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

