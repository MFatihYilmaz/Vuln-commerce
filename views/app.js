const restApi='http://127.0.0.1:5000/api'

async function checkToken() {
	if (localStorage.getItem('token')) {
		return true;
	}
	else {
		window.location.href = 'login.html';
		return false;
	}
}
function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
async function register() {
    let path=window.location.pathname
    console.log(path);
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
            document.getElementsByClassName('help-block')[0].innerHTML='Username is used'
        }
    }).catch(e=>{
        console.error(e);
    })
   
}
async function login(){
    fetch(restApi+'/login',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
        },
        body:JSON.stringify({
            user_name:document.getElementById('username').value,
            password:document.getElementById('password').value
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
        document.getElementById('profile-img').setAttribute('src','../public/uploads/'+photo_name);
       
    })
   
    document.getElementById('warning').classList.add('d-none');
    
   
    //window.location.reload();
}

async function logout(){
    localStorage.removeItem('token');
    let cookieName='userTracking'
    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    document.cookie = "profile=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    window.location.href='index.php'
}
async function inet(){
   let a=document.createElement('div');
        a.classList.add('position-absolute','bottom-30' ,'end-70','bg-success','d-none')
        a.innerHTML='Okey La Okey'
        
        setTimeout(() => {
        if(a.classList.contains('d-none')){
            document.getElementsByClassName('col')[0].appendChild(a)
            a.classList.remove('d-none')
        }
       
    }, 1000);
    
    document.getElementsByClassName('col')[0].removeChild(a)
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
                        <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
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
async function getImage() {
    let token=localStorage.getItem('token');
    fetch(restApi+'/getfile?file=back.jpg',{
        mode:"no-cors",
        headers:{
            'Authorization': 'Bearer '+token,
        }
    }).then(response => {
        
        return response.blob();
      })
      .then(imageBlob => {
        document.getElementById('profile-img').setAttribute('src',restApi+'/getfile?file=back.jpg')

      })
      .catch(error => {
        console.error('Hata oluştu: ', error);
      });
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    }

async function getProfile() {
    let photo_name=atob(decodeURIComponent(getCookie('photo')))
    document.getElementById('profile-img').setAttribute('src','../public/uploads/'+photo_name);
    let params=new URLSearchParams();
    let cookieVal=getCookie('userTracking')
    params.append('userTracking',cookieVal)
	var authHeader = localStorage.getItem('token');
	
    fetch(restApi+'/users?'+params, 
    { 
        
        headers: {  
            'Authorization': 'Bearer '+authHeader,
        } 
    })
		.then(response => response.json())
		.then(data => {
			let user=data.user
            document.getElementById('hi').innerHTML='Merhaba <b>'+user.name+' '+user.surname+'</b>';
            
		})
		.catch(error => {
			console.error(error);
		});
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
        let price=basketVal.basket_total;
        let productVal=data.products;
        console.log(productVal);
        totalPrice=`
        <div class="d-flex justify-content-between mb-4" style="font-weight: 500;">
          <p class="mb-2">Total</p>
          <p class="mb-2">$${price}</p>
        </div>

        <button type="button" class="btn btn-primary btn-block btn-lg">
          <div class="d-flex justify-content-between">
          $${price} Checkout
          </div>
        </button>`
            productVal.forEach(element => {
                
            htmlContext+=`
            <tr>
            <th scope="row">
                            <div class="d-flex align-items-center">
                              <img src="x" class="img-fluid rounded-3"
                                style="width: 120px;" alt="Book">
                              <div class="flex-column ms-4">
                                <p class="mb-2">${element.product_name}</p>
                              </div>
                            </div>
                          </th>
                          <td class="align-middle">
                            <div class="d-flex flex-row">
                              <button class="btn btn-link px-2"
                                onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                <i class="fas fa-minus"></i>
                              </button>
          
                              <input id="form1" min="1" name="quantity" value="${basketVal.orders[i][1]}" type="number"
                                class="form-control form-control-sm" style="width: 50px;" />
          
                              <button class="btn btn-link px-2"
                                onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                <i class="fas fa-plus"></i>
                              </button>
                            </div>
                          </td>
                          <td class="align-middle">
                            <p class="mb-0" style="font-weight: 500;">$${element.product_price}</p>
                          </td>
                          <td class="align-middle justify-content-end">
                            <button class="btn btn-danger" type="button">X</button>
                          </td>
                          </tr>`

                       
                       i++;
                       
                        });
            
                        document.getElementById('body').innerHTML= htmlContext
                        document.getElementById('price').innerHTML= totalPrice
        });
    

    }

    
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
        let product=data.products[0]
        if (product){
            productHTML+=`
        <div class="col-md-6">
            <img src="urun_resmi.jpg" alt="Ürün Resmi" class="img-fluid">
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
        htmlContext.innerHTML=productHTML;
        }else{
            htmlContext.innerHTML='' 
        }
        
    })
    .catch(error=>{
        console.error(error)
    })
}
async function addBasket(){
    document.getElementById('add-result').innerHTML=''
    if (checkToken()) {
        
		const urlParams = new URLSearchParams(window.location.search);
		let idVal = urlParams.get('product_id');
		var token = localStorage.getItem('token');
        fetch(restApi+'/basket',{
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
    // Buraya tekrar bak script alert çalışmıyor bazı sayfa yükeleme problemleri

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
                        <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
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
        }else{
            document.getElementById('prodCont').innerHTML=searchBar.value +' not found';
        }
        
        
    }).catch(error=>{
        console.error(error)
    })
   



}