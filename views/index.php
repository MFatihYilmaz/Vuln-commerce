<?php
    require_once '/var/www/html/apps/vendor/autoload.php';
    $name='';
    #$name = '{{ include(template_from_string("Hello {{ name }}")) }}';
    #$name = "{{}}";
    if(isset($_COOKIE['userTracking'])){
        $cookieVal=$_COOKIE['userTracking'];
        $user=unserialize(base64_decode($cookieVal));
        $name=$user['name'];
    }
    
    $ch=curl_init('http://127.0.0.1:5000/api/categories');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res=curl_exec($ch);
    $decoded=json_decode($res);
    ?>
    <?php 
    $t = '
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <link rel="icon" href="images/favicon.ico" />
            <link
                rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
                integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
                crossorigin="anonymous"
                referrerpolicy="no-referrer"
            />
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                laravel: "#ef3b2d",
                                yellow:"#fde047"
                            },
                        },
                    },
                };
            </script>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <title>Our Shop</title>
        </head>
        <body class="mb-48" onload="getAllProd()">
         
            <nav class="flex justify-between items-center mb-4">
                <a href="index.html"
                    ><img class="w-24" src="images/logo.png" alt="" class="logo"
                /></a>
                <ul class="flex space-x-6 mr-6 text-lg">
                    <li>
                        Hoşgeldin '.$name.'!
                    </li>
                    <li>
                        <a href="login.html" class="hover:text-laravel"
                            ><i class="fa-solid fa-arrow-right-to-bracket"></i>
                            Profile</a
                        >
                    </li>
                </ul>
            </nav>

            
            <!-- Hero -->
            <section
                class="relative h-72 bg-black flex flex-col justify-center align-center text-center space-y-4 mb-4"
            >
                <div
                    class="absolute top-0 left-0 w-full h-full opacity-10 bg-no-repeat bg-center"
                    style="background-image: url(\'images/laravel-logo.png\')"
                ></div>
    
                <div class="z-10">
                    <h1 class="text-6xl font-bold uppercase text-white">
                        Our<span class="text-laravel">Shop</span>
                    </h1>
                    
                    <p class="text-2xl text-gray-200 font-bold my-4">
                        Find all products
                    </p>
                    <div>
                        <a
                            href="register.html"
                            class="inline-block border-2 border-white text-white py-2 px-4 rounded-xl uppercase mt-2 hover:text-yellow hover:border-black"
                            >Sign Up to Buy something</a
                        >
                    </div>
                </div>
            </section>
    
            <main>  
            <section class=mb-3>
            <div class="container pt-5">
              <nav class="row gy-4">
                <div class="col-lg-8 mx-auto col-md-12">
                  <div class="row">
                 <div class="col-3">
                    <a href="#" class="text-center d-flex flex-column justify-content-center">
                      <button type="button" class="btn btn-outline-secondary mx-auto p-3 mb-2" data-mdb-ripple-color="dark">
                      <i class="fa-solid fa-laptop"></i>
                      </button>
                      <div class="text-dark">Electronics</div>
                    </a>
                  </div>
                   
                  <div class="col-3">
                    <a href="#" class="text-center d-flex flex-column justify-content-center">
                      <button type="button" class="btn btn-outline-secondary mx-auto p-3 mb-2" data-mdb-ripple-color="dark">
                      <i class="fa-solid fa-house"></i>
                      </button>
                      <div class="text-dark">Home</div>
                    </a>
                  </div>
                  <div class="col-3">
                  <a href="#" class="text-center d-flex flex-column justify-content-center">
                    <button type="button" class="btn btn-outline-secondary mx-auto p-3 mb-2" data-mdb-ripple-color="dark">
                    <i class="fa-solid fa-shirt"></i>
                    </button>
                    <div class="text-dark">Clothes</div>
                  </a>
                </div>
                <div class="col-3">
                <a href="#" class="text-center d-flex flex-column justify-content-center">
                  <button type="button" class="btn btn-outline-secondary mx-auto p-3 mb-2" data-mdb-ripple-color="dark">
                  <i class="fa-solid fa-list"></i>
                  </button>
                  <div class="text-dark">Others</div>
                </a>
              </div>
                   
                   </div>
                </div>
              
              </nav>
            </div>
          </section>
                
                <div
                    class="gap-4 space-y-4 md:space-y-0 mx-auto" id="container"
                >
                <div class="row flex justify-content-between">
                    <!-- Item 1 -->
                    <div class="col-4">
                    <div class="bg-gray-50 border bg-info border-gray-200 rounded p-6">
                        <div class="flex">
                           
                            <div>
                                <h3 class="text-2xl ">
                                    <a href="#">Super Campaigns
                                    </a>
                                </h3>
                                <div class="text-xl font-bold mb-4">Acme Corp</div>
                               
                        
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="col-4">
                    <div class="bg-gray-50 border border-gray-200 bg-info rounded p-6">
                        <div class="flex justify-content-end">
                           
                            <div>
                                <h3 class="text-2xl ">
                                    <a href="products.htmlgit">Cheap Products</a>
                                </h3>
                                <div class="text-xl font-bold mb-4">Retailer</div>
                               
                        
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="row flex justify-content-center">
                    <!-- Item 3 -->
                    <div class="col-4">
                    <div class="bg-gray-50 border border-gray-200 bg-warning rounded p-6">
                        <div class="flex justify-content-center">
                           
                            <div>
                                <h3 class="text-2xl">
                                    <a href="security.html">We prioritize the security of our customers</a>
                                </h3>
                                <div class="text-xl font-bold mb-4">SOC Analyst</div>
                               
                                    
                                
                               
                        
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <footer
                    class="fixed bottom-0 left-0 w-full flex items-center justify-start font-bold bg-laravel text-white h-24 mt-24 opacity-90 md:justify-center"
                >
                    <p class="ml-2">Copyright &copy; 2023, All Rights reserved</p>
                </footer>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                <script src="script.js"></script>
                <script src="app.js"></script>
            </body>
        </html>';
                    
                    
    $loader = new Twig\Loader\ArrayLoader(array('index' => $t,));
    $twig = new Twig\Environment($loader, array(
        'debug' => true,));


    $twig->addExtension(new Twig\Extension\DebugExtension());


 #   $loader.getCacheKey($t->get_class());
  #  print_r( get_class_methods($loader));
    #echo $twig));
    #echo $loader;
    #var_dump(get_class($t));

    echo $twig->render('index',['name'=>'asd']);
?>
