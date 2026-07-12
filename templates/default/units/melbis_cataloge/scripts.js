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
