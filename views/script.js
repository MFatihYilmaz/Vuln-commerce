let pass=document.getElementById('password')
let repass=document.getElementById('re-password')
let warn=document.getElementsByClassName('help-block')[0]


repass.addEventListener('keyup',(e)=> {
        e.preventDefault()
        if (pass.value!=repass.value) {
         warn.innerHTML='Password mismatch';
        }else{
       warn.classList.remove('bg-danger')
       warn.classList.add('bg-success')
       warn.textContent='Password correct'
        }
    }) 
    
    
    
    
