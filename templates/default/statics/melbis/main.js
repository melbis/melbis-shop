/***************************************************************************************************
 * @version 6.5.1.525 @ 2026-10-29
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

