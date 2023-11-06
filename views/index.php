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
            <title>LaraGigs | Find Laravel Jobs & Projects</title>
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
                
                <div
                    class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4" id="container"
                >
                    <!-- Item 1 -->
                    <div class="bg-gray-50 border border-gray-200 rounded p-6">
                        <div class="flex">
                            <img
                                class="hidden w-48 mr-6 md:block"
                                src="images/acme.png"
                                alt=""
                            />
                            <div>
                                <h3 class="text-2xl">
                                    <a href="show.html">Senior Laravel Developer</a>
                                </h3>
                                <div class="text-xl font-bold mb-4">Acme Corp</div>
                                <ul class="flex">
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Laravel</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">API</a>
                                    
                            </li>
                                
                                <li
                                
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Backend</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Vue</a>
                                    </li>
                                </ul>
                                <div class="text-lg mt-4">
                                    <i class="fa-solid fa-location-dot"></i> Boston,
                                    MA
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="bg-gray-50 border border-gray-200 rounded p-6">
                        <div class="flex">
                            <img
                                class="hidden w-48 mr-6 md:block"
                                src="images/stark.png"
                                alt=""
                            />
                            <div>
                                <h3 class="text-2xl">
                                    <a href="show.html">Full-Stack Engineer</a>
                                </h3>
                                <div class="text-xl font-bold mb-4">
                                    Stark Industries
                                </div>
                                <ul class="flex">
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Laravel</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">API</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Backend</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Vue</a>
                                    </li>
                                </ul>
                                <div class="text-lg mt-4">
                                    <i class="fa-solid fa-location-dot"></i>
                                    Lawrence, MA
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Item 3 -->
                    <div class="bg-gray-50 border border-gray-200 rounded p-6">
                        <div class="flex">
                            <img
                                class="hidden w-48 mr-6 md:block"
                                src="images/wayne.png"
                                alt=""
                            />
                            <div>
                                <h3 class="text-2xl">
                                    <a href="show.html">Laravel Developer</a>
                                </h3>
                                <div class="text-xl font-bold mb-4">
                                    Wayne Enterprises
                                </div>
                                <ul class="flex">
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Laravel</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">API</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Backend</a>
                                    </li>
                                    <li
                                        class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
                                    >
                                        <a href="#">Vue</a>
                                    </li>
                                </ul>
                                <div class="text-lg mt-4">
                                    <i class="fa-solid fa-location-dot"></i> Newark,
                                    NJ
                                </div>
                            </div>
                        </div>
                    </div>
                    <footer
                    class="fixed bottom-0 left-0 w-full flex items-center justify-start font-bold bg-laravel text-white h-24 mt-24 opacity-90 md:justify-center"
                >
                    <p class="ml-2">Copyright &copy; 2022, All Rights reserved</p>
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
