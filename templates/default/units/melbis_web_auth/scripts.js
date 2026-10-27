
// Auth
$('#melbis_form_auth').submit(function(event)
{    
    $('#melbis_form_auth button[type="submit"]').html('Please wait...').attr('disabled','disabled');      
    var login = $('#melbis_form_auth input[name="login"]').val();
    var pass = $('#melbis_form_auth input[name="pass"]').val();

    // Password stays, sign goes
    melbis_crypto_sign(login, pass).then(function(auth)
    {
        melbis_auth_send(login, auth);
    }, function()
    {
        // Old browser or no https
        bootbox.alert({
            title: 'Browser not supported',
            message: 'Sorry, this browser cannot sign in: use a recent one over https!'
        });
        $('#melbis_form_auth button[type="submit"]').html('Sign in').removeAttr('disabled');
    });

    event.preventDefault();
    return false;
});


// Send the signed login
function melbis_auth_send(login, auth)
{
    $.post(melbis_page_mod,        
        { 
            form_auth: true,
            login: login,
            stamp: auth.stamp,
            sign: auth.sign
        },
        function(data) 
        {                                                    
            if ( data.result == 'ACCEPT'  )
            {  
                location.reload();                                
            } 
            else if ( data.result == 'DENIED' )
            {      
                bootbox.alert({
                    title: 'Access denied',
                    message: 'Sorry, you do not have access to this module!'                        
                })                                             
            }
            else if ( data.result == 'WRONG' )
            {      
                bootbox.alert({
                    title: 'Authentication fault',
                    message: 'Sorry, but login or password is incorrect!'                        
                })                                             
            }     
            $('#melbis_form_auth button[type="submit"]').html('Sign in').removeAttr('disabled');                             
        },
        'json'
    );   
}


