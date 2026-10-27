/***************************************************************************************************
 * @version 6.5.1.520 @ 2026-10-27
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


// Sign login|stamp|WEB by the password
function melbis_crypto_sign(login, pass)
{
    var enc = new TextEncoder();
    var salt = enc.encode(login + '@' + location.host.toLowerCase());
    // GMT stamp, program's shape
    var stamp = new Date().toISOString().slice(0, 19).replace('T', ' ');
    var line = enc.encode(login + '|' + stamp + '|WEB');

    // No crypto.subtle throws inside the chain
    return Promise.resolve().then(function()
    {
        return crypto.subtle.importKey('raw', enc.encode(pass), 'PBKDF2', false, ['deriveBits']);
    }).then(function(base)
    {
        // Seed: same rounds as program
        return crypto.subtle.deriveBits({name: 'PBKDF2', hash: 'SHA-256', salt: salt, iterations: 400000}, base, 256);
    }).then(function(seed)
    {
        // Ed25519 key wrapped as PKCS#8
        var pkcs8 = new Uint8Array(48);
        pkcs8.set([0x30, 0x2e, 0x02, 0x01, 0x00, 0x30, 0x05, 0x06, 0x03, 0x2b, 0x65, 0x70, 0x04, 0x22, 0x04, 0x20], 0);
        pkcs8.set(new Uint8Array(seed), 16);
        return crypto.subtle.importKey('pkcs8', pkcs8, {name: 'Ed25519'}, false, ['sign']);
    }).then(function(key)
    {
        return crypto.subtle.sign({name: 'Ed25519'}, key, line);
    }).then(function(sig)
    {
        var bin = String.fromCharCode.apply(null, new Uint8Array(sig));
        return {stamp: stamp, sign: btoa(bin)};
    });
}



// Navbar
//==============================================================================

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
            $(item).addClass('active');
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
//==============================================================================

// Add to basket - the button carries its goods, so the markup around it is free
$(document).on('click', '.melbis_btn_add', function(event)
{
    var name = $(this).data('name');
    $.post('/?mod=melbis_basket',        
        { func: 'Plus', id: $(this).data('id') },
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
        { func: 'Minus', id: $(this).data('id') },
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
    $.post('/?mod=melbis_basket', { func: 'Fields' }, function(data) { $('#melbis_order_fields').html(data); } );
    $.post('/?mod=melbis_basket', { func: 'Goods' }, function(data) { $('#melbis_order_goods').html(data); } );
    $.post('/?mod=melbis_basket', { func: 'Options' }, function(data) { $('#melbis_order_options').html(data); } );       
});


// Finish
$('#melbis_btn_finish').on('click', function(event)
{
    for( var item of $('#melbis_form_order select') )
    { 
        $(item).prev('input').val($('option:selected', $(item)).text());
    }
        
    var form = $('#melbis_form_order').melbis_serial();
    form.func = 'Save';
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
