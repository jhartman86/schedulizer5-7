<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpKernel\HttpKernel;
    use \Symfony\Component\EventDispatcher\EventDispatcher;
    use \Symfony\Component\HttpKernel\Controller\ControllerResolver;
    use \Symfony\Component\HttpKernel\EventListener\RouterListener;
    use \Symfony\Component\Routing\RouteCollection;
    use \Symfony\Component\Routing\Matcher\UrlMatcher;
    use \Symfony\Component\Routing\RequestContext;

    class OnStart {

        public function __construct( \Closure $closure ){
            $this->routeCollection = new RouteCollection();
            $closure($this->routeCollection);
            $this->setup();
        }


        protected function setup(){
            try {
                $request    = Request::createFromGlobals();
                $request->enableHttpMethodParameterOverride();
                $matcher    = new UrlMatcher($this->routeCollection, new RequestContext());
                $dispatch   = new EventDispatcher();
                $dispatch->addSubscriber(new RouterListener($matcher));
                $resolver   = new ControllerResolver();
                $kernel     = new HttpKernel($dispatch, $resolver);
                $response = $kernel->handle($request);
                $response->send();
                $kernel->terminate($request, $response);
                exit(0);
            }catch(\Exception $e){
                // No event found, punt back up to Concrete5 runtime
            }
        }

    }

}