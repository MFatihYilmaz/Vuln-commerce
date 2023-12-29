const restApi='http://api.vulncommerce:5051/api'

async function checkToken() {
	if (localStorage.getItem('token')) {
		return true;
	}
	else {
		window.location.href = 'login.html';
		return false;
	}
}
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length == 2) return parts.pop().split(';').shift();
    }

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
async function register() {
    fetch(restApi+'/register',{
        method:'POST',
        headers:{
            'Content-Type': 'application/json',
            'Accept':'application/json'
        },
        body:JSON.stringify({
            user_name:document.getElementById('username').value,
            name:document.getElementById('name').value,
            surname:document.getElementById('surname').value,
            password:document.getElementById('password').value
        })
    }).then(response=>response.json())
    .then(data=>{
        if(data.status==200){
            
            window.location='login.html'
        }else{
            document.getElementsByClassName('help-block')[0].classList.add('bg-danger');
            document.getElementsByClassName('help-block')[0].innerHTML='Username is used'
        }
    }).catch(e=>{
        console.error(e);
    })
   
}

async function resetPassword(){
    let body=document.getElementById('card-body')
    fetch(restApi+'/resetpass',{
        method:'POST',
        headers:{
            'Content-Type': 'application/json',
            'Host':'127.0.0.1:80'
            
        },
        body:JSON.stringify({
            user_name:document.getElementById('username').value
        })
    }).then(response=>response.json())
    .then(data=>{
        if(data.link){
            var link=data.link
            const domainRegex  = /^(?:https?:\/\/)?(?:www\.)?([^\/:]+)(?::(\d+))?\/?/;
            var match=link.match(domainRegex);
            if (match[2]) {
                console.log(match[1]+':'+match[2]);
                link=link.replace(match[1]+':'+match[2],'127.0.0.1/apps/views')
                
            }else{
                link=link.replace(match[1],'127.0.0.1/apps/views')
            }
            document.getElementsByClassName('warning')[0].innerHTML="";
            if(body.lastChild.textContent=='Reset Link'){
                body.removeChild(body.lastChild)
            }
            let reset=document.createElement('a')        
            reset.text='Reset Link';
            reset.setAttribute('href',link)
            body.appendChild(reset)
        }else{
            if(body.lastChild.textContent=='Reset Link'){
                body.removeChild(body.lastChild)
            }
            document.getElementsByClassName('warning')[0].innerHTML=data.message;
        }
    })
}

async function login(captcha){
    fetch(restApi+'/login',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
        },
        body:JSON.stringify({
            user_name:document.getElementById('username').value,
            password:document.getElementById('password').value,
            g_captcha:captcha
        })
    })
    .then(response=>response.json())
    .then(data=>{
        
        if(data.token){
            localStorage.setItem('token',data.token);
            setCookie('userTracking',data.tracking,60*60*60*60);
            window.location.href='profile.html'
        }else{
            document.getElementsByClassName('warning')[0].innerHTML=data.error;
        }
       
    })
    .catch(error => {
        console.error(error);
    });
    
    
}

function changePass(){
    var urlParams=new URLSearchParams(window.location.search);
    let token=urlParams.get('token')
    fetch(restApi+'/changepass?token_id='+token,{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
        },
        body:JSON.stringify({
            pass:document.getElementById('password').value,
            repass:document.getElementById('re-password').value
        })
    })
    .then(response=>response.json())
    .then(data=>{
        if(data.status==200){
            window.location.href='login.html'
        }else{
         document.getElementsByClassName['warning'][0].innerHTML=data.message;
        }
    })
}

function logout(){
    localStorage.removeItem('token');
    let cookieName='userTracking'
    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    document.cookie = "profile=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    window.location.href='index.php'
}

async function fileUpload(){
    let token=localStorage.getItem('token')
    let formData = new FormData();           
    formData.append("file",myFile.files[0]);
    fetch('../contoller/files.php',{
        method:'POST',
        headers:{
            'Authorization': 'Bearer '+token
        },
        body:formData
    }).then(response=>response.json()
    ) .then(data=>{
        let a =document.getElementById('warning')
        a.innerHTML=data.message;
        setTimeout(()=>{
            if(a.classList.contains('d-none')){
                a.classList.remove('d-none')
            }
           
        },2000)
        let photo_name=atob(decodeURIComponent(getCookie('photo')))
        document.getElementById('profile-img').setAttribute('src','./uploads/'+photo_name);
        document.getElementById('nav-img').setAttribute('src','./uploads/'+photo_name);
       
    })
   
    document.getElementById('warning').classList.add('d-none');
    
   
    //window.location.reload();
}

async function getAllProd(){
    let productHTML=""
    fetch(restApi+'/products/all')
    .then(response=>response.json())
    .then(data=>{
        data.forEach(prod => {
            productHTML+=`
            <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Sale badge-->
                        <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                        <!-- Product image-->
                        <img class="card-img-top" src="http://127.0.0.1:5000/api/getfile?file=${prod.product_image}" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">`+prod.product_name+`</h5>
                               
                               
                                <!-- Product price-->
                                <span class="text-muted text-decoration-line-through">$20.00</span>
                                $`+prod.product_price+`
                            </div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="details.html?product_id=`+prod.product_id+`">Go to details</a></div>
                        </div>
                    </div>
                </div>
            `
        });
        document.getElementById('prodCont').innerHTML=productHTML;
       
    }).catch(error=>{
        console.error(error)
    })
    
    
}

async function setAddress(){
    let token=localStorage.getItem('token')
    let username=payloadsJWT(token).username;
   
    await fetch(restApi+'/addresses/'+username,{
        method:'POST',
        headers:{
            'Content-Type': 'application/json',
            'Authorization': 'Bearer '+token
        },
        body:JSON.stringify({
            address_header:document.getElementById('addhead').value,
            address:document.getElementById('addbody').value
        })
    }).then(response=>response.json())
    .then(data=>{
        console.log(data);
    }).catch(err=>{
        console.log(err);
    })
}

async function getAddress() {
    let token = localStorage.getItem('token')
 let username=payloadsJWT(token).username
 let address=document.getElementById('addressbar')
 let htmlContent=""
 fetch(restApi+'/addresses/'+username,{
    headers:{
        'Authorization':'Bearer '+token,
    }
 }).then(response=>response.json())
 .then(data=>{
    let value=data.addresses
    if(value){
        htmlContent=`
        <div class="form-check ">
        <input type="radio" class="form-check-input" id="adres1" name="secilenAdres" style=" border: 2px solid #3498db;" value="${value.address_header}">
        <label for="adres1">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">${htmlEntities(value.address_header)}</h5>
            <p class="card-text">${htmlEntities(value.address)}</p>
          </div>
        </div>
      </label>
      </div>`
    }
    
    address.insertAdjacentHTML('afterbegin',htmlContent)
 }) 
}

async function getProfile() {
    if(getCookie('photo')){
        let photo_name=atob(decodeURIComponent(getCookie('photo')))
        document.getElementById('profile-img').setAttribute('src','./uploads/'+photo_name);
        document.getElementById('nav-img').setAttribute('src','./uploads/'+photo_name)
    }

    let params=new URLSearchParams();
    let cookieVal=getCookie('userTracking')
    params.append('userTracking',cookieVal)
	var authHeader = localStorage.getItem('token');
    fetch(restApi+'/users?'+params, 
    { 
        headers: {
            'Content-Type':'application/json',
            'Cookie':params,
            'Authorization': 'Bearer '+authHeader,
        } 
    })
		.then(response => response.json())
		.then(data => {
            console.log(data);
			let user=data.user
            console.log(user);
            if(user.user_role==1){
                navLink=`
                <li class="nav-item">
                <a class="nav-link mx-2 text-black" id='admin' onclick="adminfunc()" href="#">Admin</a>
              </li>
                `   
                document.getElementsByClassName('navbar-nav')[0].insertAdjacentHTML('beforeend',navLink) 
            }
            document.getElementById('hi').innerHTML=' Merhaba <b>'+htmlEntities(user.name)+' '+htmlEntities(user.surname)+'</b> ';
            
		})
		.catch(error => {
			console.error(error);
		});
    
}

async function addDeposit(event){
    let token=localStorage.getItem('token')
    
    fetch(restApi+'/deposit/load',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
        },
        body:JSON.stringify({
            deposit:document.getElementById('deposit').value,
            cardNum:document.getElementById('card-num').value,
            cardName:document.getElementById('card-name').value,
            expTime:document.getElementById('exp').value,
            cvv:document.getElementById('cvv').value
        })
    }).then(response=>response.json())
    .then(data=>{
        document.getElementById('warning-card').innerText=data.status;
        event.parentNode.parentNode.parentNode.reset()
    })
}

async function getOrdersDetail(){
    let token = localStorage.getItem('token')
   let currentUrl = window.location.href;
	let params = new URLSearchParams(currentUrl.split('?')[1]);
    let id=params.get('id')
    let orderHtml=''
    fetch(restApi+'/orders/'+id,{
        headers:{
            'Authorization':'Bearer '+token
        },

    }).then(response=>response.json())
    .then(data=>{
        console.log(data);
        if(!data.message){
            
            let orders=data.orders[0].orders
            //console.log(orders);
            const mapping=orders.map(order=>{
                const product=data.products.find(prod=>prod.product_id==order[0])
                return {...order,product}
            })
            
            data.orders.forEach(element => {
                orderHtml+=`
                <div class="col-lg-5 col-xl-6">
              <div class="card border-top border-bottom border-3" style="border-color: #f37a27 !important;">
                <div class="card-body p-5">
      
                  <p class="lead fw-bold mb-5" style="color: #f37a27;">Purchase Reciept</p>
      
                  <div class="row">
                    <div class="col mb-3">
                      <p class="small text-muted mb-1">Date</p>
                      <p>${element.basket_date}</p>
                    </div>
                    <div class="col mb-3">
                      <p class="small text-muted mb-1">Order No.</p>
                      <p>${element.basket_id}</p>
                    </div>
                  </div>`
                mapping.forEach(el=>{
                    orderHtml+=`
                    <div class="mx-n5 px-5 py-4" style="background-color: #f2f2f2;">
                    <div class="row">
                    <div class="col-md-2 col-lg-2">
                        <p>${el[1]}</p>
                      </div>
                      <div class="col-md-7 col-lg-8">
                        <p>${el.product.product_name}</p>
                      </div>
                      <div class="col-md-3 col-lg-2">
                        <p>${el[2]}</p>
                      </div>
                    </div>
               
                  </div>`
                })
      
                orderHtml+=`
                  <div class="row my-4">
                    <div class="col-md-4 offset-md-8 col-lg-3 offset-lg-9">
                      <p class="lead fw-bold mb-0" style="color: #f37a27;">${element.basket_total}</p>
                    </div>
                  </div>
      
      
      
                    </div>
                </div>
                </div>`
                                       
               });
                
                  document.getElementById('ordercontent').innerHTML=orderHtml 
        }else{
            document.getElementById('ordercontent').innerHTML=data.message
        }   
      
    })
   
}
async function getOrders() {
    let token=localStorage.getItem('token')
    let orderHtml='';
    fetch(restApi+'/orders/all',{
        headers:{
            'Authorization':'Bearer '+token
        }
    }).then(response=>response.json())
    .then(data=>{
        if(!data.message){
            let orders=data.orders
            console.log(orders);
            orders.forEach(element => {
                orderHtml+=`
                <div class="col-lg-5">
                      <div class="card border-top border-bottom border-3" style="border-color: #f37a27 !important;">
                        <div class="card-body p-5">
              
                         <a class="lead fw-bold mb-5" mx-2 text-black" href=./orders.html?id=${element.basket_id}>#${element.basket_id}</a>
              
                          <div class="row">
                            <div class="col mb-3">
                              <p class="small text-muted mb-1">Date</p>
                              <p>${element.basket_date}</p>
                            </div>
                            <div class="col mb-3">
                              <p class="small text-muted mb-1">Order No.</p>
                              <p>${element.basket_id}</p>
                            </div>
                        </div>
                        </div>
                            </div>
                            </div>`
               });
                
                  document.getElementById('ordercontent').innerHTML=orderHtml 
        }else{
            document.getElementById('ordercontent').innerHTML=data.message
        }

    })
}

async function adminfunc() {
    if(getCookie('photo')){
        var photo_name=atob(decodeURIComponent(getCookie('photo')))
    }
    
    let token=localStorage.getItem('token');
    let users=''
       
    fetch(restApi+'/users/all',{
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
        }
    }).then(response=>response.json())
    .then(data=>{
        if(data.users){
            data.users.forEach(element => {
                users+=`
                <tr>
        <td>
        <p class="fw-normal text-center mt-2">${element.user_id}</p>
        </td>
      <td>
        <div class="d-flex align-items-center">
          <img
              src="./uploads/${photo_name}"
              alt=""
              style="width: 45px; height: 45px"
              class="rounded-circle"
              />
          <div class="ml-3">
            <p class="fw-bold mb-1">${element.name}</p>
          </div>
        </div>
      </td>
      <td>
      <p class="fw-normal mb-1">${element.surname}</p>
        
      </td>
      
      <td>
      <a href="#" onclick="removeUser(this)">Delete</a>
        
      </td>
    </tr>

                `
            });

        
        }
        let htmlContent=`
        <div id='ok' class="row mt-3">
                <div class="container">

                    <table class="table align-middle mb-0 bg-white">
                        <thead class="bg-light">
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        ${users}

                        </tbody>
                    </table>




                </div>


            </div>
        `
        if(!document.getElementById('ok')){
            document.getElementById('cont').insertAdjacentHTML('afterend',htmlContent)
        }
       
    }).catch(err=>{
        console.error(err);
    })
}

async function removeUser(clicked){
    let token=localStorage.getItem('token')
    var id = clicked.parentNode.parentNode.firstElementChild.firstElementChild.innerText;
   
    fetch(restApi+'/users/remove/'+id,{
        headers:{
            'Authorization':'Bearer '+token
        }
    }).then(response=>response.json())
    .then(data=>{
        if(data.message=='Success'){
            window.location.reload()
        }
    }).catch(err=>{
        console.error(err);
    })
}


async function updateBasket(click){
    let id =click.classList[click.classList.length-1]
    console.log(id);
    var quantity;
    if(click.classList.contains('left')){
        quantity=click.nextElementSibling.value
    }else{
        quantity=click.previousElementSibling.value
    }
   
    console.log('-----------'+quantity);
    let token=localStorage.getItem('token')
    fetch(restApi+'/basket/update',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token,
            
        },
        body:JSON.stringify({
            product_id:id,
            quantity:quantity
        })
    }).then(response=>response.json())
    .then(data=>{
        console.log(data);
        getBasket()
    })
   
}



async function getBasket() {
    let htmlContext='';
    let totalPrice='';
    if(checkToken()){
        let token=localStorage.getItem('token')
        fetch(restApi+'/getcart',{
            headers:{'Authorization':'Bearer '+token
        }}
        
    ).then(response=>response.json())
     .then(data=>{
        let i=0;
        
        let basketVal=data.basket;
        if(basketVal!=""){

      
        let price=basketVal.basket_total;
        let productVal=data.products;
            totalPrice=`
            <div class="d-flex justify-content-between px-4 mb-4">
            <div class="col-md-4 mb-4 mr-4" style="font-weight: 500;">
              <p class="mb-2">Total</p>
              <p class="mb-2">$${price}</p>
            </div>
            <input id='price' name='price' type='hidden' value=${price}>
            <button type="button" onclick="buy()" class="col-md-8 btn btn-primary btn-block btn-lg">
              <div class="d-block ">
              $${price} Checkout
              </div>
            </button>
            
            `
                productVal.forEach(element => {
                    
                htmlContext+=`
                <tr>
                <th scope="row">
                                <div class="d-flex align-items-center">
                                  <img src="#" class="img-fluid rounded-3"
                                    style="width: 120px;" alt="Book">
                                  <div class="flex-column ms-4">
                                    <p class="mb-2">${element.product_name}</p>
                                  </div>
                                </div>
                              </th>
                              <td class="align-middle">
                                <div class="d-flex flex-row">
                                  <button  class="btn btn-link left px-2 ${element.product_id}"
                                    onclick="this.parentNode.querySelector('input[type=number]').stepDown();updateBasket(this);">
                                    <i class="fas fa-minus"></i>
                                  </button>
              
                                  <input id="form1" min="1" max="10" name="quantity" oninput="validity.valid||(value='');" value="${basketVal.orders[i][1]}" type="number"
                                    class="form-control form-control-sm" style="width: 50px;" />
              
                                  <button class="btn btn-link right px-2 ${element.product_id}"
                                    onclick="this.parentNode.querySelector('input[type=number]').stepUp();updateBasket(this);">
                                    <i class="fas fa-plus"></i>
                                  </button>
                                </div>
                              </td>
                              <td class="align-middle">
                                <p class="mb-0" style="font-weight: 500;">$${element.product_price}</p>
                              </td>
                              <td class="align-middle justify-content-end">
                              <input name='id' type='hidden' value=${element.product_id}>
                                </td>
                              </tr>`
    
                           
                           i++;
                           
                            });
                        }else{
                            htmlContext=` 
                            <tr>
                            <th scope="row">
                                            <div class="d-flex align-items-center">
                                            
                                              <div class="flex-column ms-4">
                                                <p class="mb-2">There is no item in basket</p>
                                              </div>
                                            </div>
                                          </th>
                                    </tr>`
                        }
            
                        document.getElementById('body').innerHTML= htmlContext
                        document.getElementById('price-div').innerHTML= totalPrice
        });
    

    }

    
}
async function buy(){
    let token=localStorage.getItem('token');
    var errorElement = document.getElementById("error");
    var coupon=document.getElementById('coupon-container');
    fetch(restApi+'/buy',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token,
           
        },
        body:JSON.stringify({
            amount:document.getElementById('price').value
        })
    }).then(response=>response.json())
    .then(data=>{
           
        if(data.message!='ok'){
        
            errorElement.innerHTML = data.message;
        errorElement.classList.remove('d-none');
        errorElement.classList.add('d-inline')
        timer = setTimeout(function(){ errorElement.classList.add('d-none'); errorElement.innerHTML=""; }, 2000);
    
        }else{
            coupon.classList.add('d-none')
        }
    getBasket()  
    })
    
}
async function sendMessage(){
    console.log('ads');
    let token=localStorage.getItem('token');
    let user=document.getElementById('message-user').value
    let message=document.getElementById('message-body').value
    let xml=document.implementation.createDocument(null,'ticket');
    var from=xml.createElement('from')
    from.textContent=user
    var msg=xml.createElement('message')
    msg.textContent=message
    xml.getElementsByTagName('ticket')[0].appendChild(from)
    xml.getElementsByTagName('ticket')[0].appendChild(msg)
    var xmlString = '<?xml version="1.0"?>\n' + new XMLSerializer().serializeToString(xml);
    
    let req=new XMLHttpRequest();
    req.open('POST',restApi+'/ticket',true);
    req.setRequestHeader('Content-Type','text/xml');
    req.setRequestHeader('Authorization','Bearer '+token);
    req.onreadystatechange=function(){
        if(req.status==200 && req.readyState==4){
    }
   
    }  
req.send(xmlString); 
}

async function clearBasket(){
    let token=localStorage.getItem('token')
    fetch(restApi+'/basket/clear',{
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
        }
    }).then(response=>response.json())
    .then(data=>{
        if(data.status!=200){
            document.getElementById('warning').innerText=data.message
        }
        window.location.reload()
    })
}


async function detailProduct(){
    let currentUrl = window.location.href;
	let params = new URLSearchParams(currentUrl.split('?')[1]);
    let prod_id=params.get('product_id')
    if(!params.has('product_id')||!prod_id.length>0 ){
        window.location.href='products.html'
    }
    let productHTML=''
    let htmlContext=document.getElementsByClassName('row')[0];
    fetch(restApi+'/products/'+prod_id)
    .then(resp=>resp.json())
    .then(data=>{        
        let products=data.products
        if (products){
            products.forEach(product=>{
                productHTML+=`
                <div class="col-md-6">
                    <img src="${restApi}/getfile?file=${product.product_image}" alt="Ürün Resmi" class="img-fluid">
                </div>
                <div class="col-md-6 product-info">
                    <h2>`+product.product_name+`</h2>
                    <p>Ürün Açıklaması:`+product.product_description+`</p>
                    <p>Fiyat:`+product.product_price+`</p>
                    <form onsubmit='event.preventDefault()' class="d-flex align-items-center">
                        <div class="form-group">
                            <label for="quantity">Adet:</label>
                            <input type="number" class="form-control quantity-input" id="quantity" name="quantity" min="1" value="1">
                        </div>
                        <button type="submit" class="btn add-to-cart-btn ml-3 mt-3" onclick='addBasket()'>Add to basket</button>
                    </form>
                </div>
                `
            })
           
        htmlContext.innerHTML=productHTML;
        }else{
            htmlContext.innerHTML='Product not found' 
        }
        
    })
    .catch(error=>{
        console.error(error)
    })
}



async function checkStock(){
    var token = localStorage.getItem('token');
    let apiStock="http://127.0.0.1/stock.php"
    const response = await fetch(restApi + '/checkstock', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            stockApi: apiStock
        })
    });

    if (!response.ok) {
        
        console.error(response.text());
    }

    const data = await response.json();
    return data;
       
}
async function addBasket(){
    document.getElementById('add-result').innerHTML=''
    let resp=await checkStock()
    let tokenControl=checkToken()
    if (tokenControl && resp) {
		const urlParams = new URLSearchParams(window.location.search);
		let idVal = urlParams.get('product_id');
		var token = localStorage.getItem('token');
      await fetch(restApi+'/basket',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Authorization':'Bearer '+token
            },
            body:JSON.stringify({
                product_id:idVal,
                quantity:document.getElementById('quantity').value
            })
        }).then(response=>response.json())
        .then(data=>{
            console.log(data);
            document.getElementById('add-result').innerHTML=data.message+'<i class="fa-solid fa-check"></i>'
        }).catch(err=>{
            console.error(err)
        })
    }
}


async function searchVal() {
    let searchBar=document.getElementById('search')
    console.log(searchBar.value)
        let productHTML=''
    
    fetch(restApi+'/search/'+searchBar.value)
    .then(response=>response.json())
    .then(data=>{
        console.log(data)
        if(data.products){
            products=data.products;
        console.log(products)
        products.forEach(prod => {
            console.log(prod)
            productHTML+=`
            <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Sale badge-->
                        <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                        <!-- Product image-->
                        <img class="card-img-top" src="${restApi}/getfile?file=${prod.product_image}" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">`+prod.product_name+`</h5>
                               
                               
                                <!-- Product price-->
                                <span class="text-muted text-decoration-line-through">$20.00</span>
                                $`+prod.product_price+`
                            git</div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="details.html?product_id=`+prod.product_id+`">Go to details</a></div>
                        </div>
                    </div>
                </div>
            `
        });
        document.getElementById('prodCont').innerHTML=productHTML;
        }else{
            let sanitizied=htmlEntities(searchBar.value)
            document.getElementById('prodCont').innerHTML=sanitizied+' not found';
        }
        
        
    }).catch(error=>{
        console.error(error)
    })
   


}
function verifyCaptcha() {
    var response = grecaptcha.getResponse();
    if(response!=""){
        login(true)
    }else{
        login(false)
    }  
    
}

function addCode(){
    let token=localStorage.getItem('token');
    fetch(restApi+'/coupon/add',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
        },
        body:JSON.stringify({
            coupon:document.getElementById('coupon').value
        })
    }).then(response=>response.json())
    .then(data=>{
        let codeElement=document.getElementById('coupon-container');
        let promo=document.getElementById('promo')
        let codeBtn=document.getElementById('codeBtn')
        if(data.message=="Code applied"){
            codeElement.lastElementChild.classList.remove('d-none')
            promo.classList.add('bg-success')
            promo.innerText=document.getElementById('coupon').value
            promo.classList.remove('bg-danger')
            codeElement.classList.remove('d-none')
            codeBtn.disabled=true
            getBasket()
        }else{
                codeElement.lastElementChild.classList.add('d-none')
                promo.innerHTML=data.message
                promo.classList.remove('bg-success')
                promo.classList.add('bg-danger')
                codeElement.classList.remove('d-none')
        
            
            setTimeout(function () {
                codeElement.classList.add('d-none')
            }, 3000);

        }
    }).catch(
        error=>console.log(error)
    )
}
function removeCode(){
    let token=localStorage.getItem('token');
    fetch(restApi+'/coupon/remove',{
        headers:{
            'Authorization':'Bearer '+token
        }
    }).then(response=>response.json()
    ).then(data=>{
        let codeBtn=document.getElementById('codeBtn')
        let codeElement=document.getElementById('coupon-container');
        if (data.message=='OK'){
            codeElement.classList.add('d-none')  
            codeBtn.disabled=false;
           
        }
        getBasket()
    })
}


function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\//g, "//");
}

function payloadsJWT(token){
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace('-', '+').replace('_', '/');
    const decoded = JSON.parse(atob(base64));
    return decoded
}
