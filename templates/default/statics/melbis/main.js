/***************************************************************************************************
 * @version 6.5.0.319 @ 2026-07-13
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

