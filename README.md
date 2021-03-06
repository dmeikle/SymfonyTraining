Symfony Notes

src directory holds bundles
app directory holds framework


To show a list of router paths (good for debugging)
app/console router:debug

within the yml file you can specify constraints in the routing with the 'requirements' key:
eg:
hello:
    path:     /hello/{userName}/{page}
    defaults: { _controller: MainBundle:Hello:index }
    requirements: 
        userName: bike|car
        page:\d+
        
when adding a CSS bundle (such as Bootstrap):
app/console assets:install

~then use asset('path-to-bundle') in the include so that it's environmentally generic

create a new Bundle:
app/console generate:bundle

create a new Entity
app/console generate:entity

generate a new Database
doctrine:database:create

generate a schema for the database
doctrine:schema:create

to update the schema:
doctrine:schema:update --force (this will overwrite everything - be careful!)

you can use:
doctrine:schema:update --dump.sql (this will display the deltas so you can edit and run manually from there)

inside app/config there are files to add to git ignore - especially parameters.yml

/vendor/composer/autoload_namespaces.php is where composer adds libraries for framework to find
~ vendor/autoload.php is needed by your project to use composer******

speed up autoloading:
    composer dump-autoload --optimize
    Internally, this builds the big class map array in vendor/composer/autoload_classmap.php.
or
    use APC class loader
    
add symlink option to composer so the 'assets' function of symfony runs better


SonataAdminBundle can be used to autogenerate your CRUD


to alias a URL with get/post methods:
product_homepage:
    pattern:  /products/new
    defaults: { _controller: ProductBundle:Default:new }
    methods: [GET]

product_save:
    pattern:  /products/new
    defaults: { _controller: ProductBundle:Default:save }
    methods: [POST]

    
    
You 'could' rename a controller folder (for example) to controller2, you would need to edit your routing.yml inside the bundle to
use the full controller path instead of the short cut, and also the complete method name (eg: indexAction)

@Route and @Method
using the sensioFrameworkBundle you can use routing as an annotation rather than a routing.yml file.  You need to point symfony to the 
controller directory and it will parse out the annotations automatically. This is done in the main routing.yml file, rather than
pointing to the bundle yml file, point it to the controller.
eg:
class PostController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        // ...
    }
}


determine what twig sees for configuration and use:
 app/console config:dump-reference twig
 
 if you want to override a block in twig, you can create a twig file with the redefined block, then 
 add it to the config.yml file so that it gets recognized:
 # Twig Configuration
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"
    form:
      resources:
        - ::form_theme.html.twig #this is an override we did that is global

        
        
to create a custom field display:
    <div class="text_widget">
        <input type="text" id="{{ form.price.vars.id }}" value="{{ form.name.vars.value }}" />
    </div>
    
for debug you can use {{ dump(form.name.vars) }}
to get the keys you can {{ dump(form.name.vars|keys) }}


to generate a url outside of the controller, pass in the router:
    $this->container->get('router')
and call its:
    generateUrl(key, params);
    

inside the services.yml if you want to pass a service in as a parameter use the '@' sign:
    my_product_serializer:
    class: %product.serializer.class%
    arguments: [@router, "plain_value", %parameter%]

to delete the cache for production:
sudo app/console cache:clear --env=prod


you can configure bundle-wide params like this (in the config.yml and/or bundle/config.yml file):

    product: #this is the name of the bundle (ProductBundle)
        foo: baz
        nashville: true
this is loaded by the bundle's extension class.




tags are ways to get symfony to recognize your listener needs better priority.  This is like raising
your hand and telling symfony you need to be recognized differently:

my_language_listener:
        class: %product.path%\EventListener\LanguageListener
        arguments: [@logger]
        tags:
            -
              name: kernel.event_listener
              event: kernel.request
              method: onKernelRequest

              
***this is the section on observer/observables functionality
doctrine callbacks - pre-persist - this is like a trigger in mysql
**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks() <-- important
 */
class Product
{
    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }
}

or you could do it as a service:
services:
    my.listener:
        class: nashville\ProductBundle\EventListener\SearchIndexer
        tags:
            - {name: doctrine.event_listener, event: postPersist}
class SearchIndexer
{
    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        
        if($entity instance of Product) {
            //do something with the product
        }
    }
}
**************** end observers/observables functionality

never thought about this:
    an event dispatcher is simply the observer pattern (I used often) that is handled by a manager instead... (mind = blown)

to get the request parameters/variables or response object, it's in the HttpFoundation object


exceptions:
$product = $this->getDoctrine()
        ->getRepository('ProductBundle:Product')
        ->findOneById($id);

if (!$product) {
    throw $this->createNotFoundException(
        'No product found for id '.$id
    );
}
and inside the base controller:
public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
{
    return new NotFoundHttpException($message, $previous);
}


we can add 'Product' as a key in the yml config because we have a bundle named ProductBundle - this makes it recognized by Symfony


when talking about passing an object into a class (preferred) vs pulling it from the container inside the method,
the term 'service location' was used to describe injecting the whole container, which is phlisophically messy.


unique host source requests:
foo:
    path: /products
    defaults: {}
    methods: [POST, PUT]
    host: foo.example.com
foo:
    path: /products
    defaults: {_controller: ...}
    methods: [POST, PUT]
    
doctrine common library allows you to put your own notations in a framework
sensioLabsBundle does the same, but it's symfony specific - works in symfony only

embedded request in twig:
 {{ render(controller('MainBundle:Hello:_latestTweets')) }}
 then you just need a method in MainBundle\HelloController called _latestTweetsAction()
 you could also use render_esi for edge side include (cached)
 to see the include source you could use:
 {{ render_hinclude(controller('MainBundle:Hello:_latestTweets')) }}
 
to use unique params per each logged in user use:
{{ render(controller('MainBundle:Hello:_latestTweets', 'limit': app.user.tweetsLimit)) }}
where app.user is available in twig.
DO NOT use:
$limit = $this->getUser()->getTweetsLimit();
if you do, the limit will cache for everyone based on the first user's limit

 easiest way to do authentication is in security.yml

   access_control:
    - { path: ^/contact, roles: IS_AUTHENTICATED_ANONYMOUSLY}
    - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY}
    - { path: ^/products/new, roles: IS_AUTHENTICATED_FULLY}
    - { path: ^/admin, roles: ROLE_ADMIN}
   # - { path: ^/main, roles: IS_AUTHENTICATED_FULLY, requires_channel: https }
      IS_AUTHENTICATED_FULLY
      IS_AUTHENTICATED_REMEMBERED <--uses a cookie
      IS_AUTHENTICATED_ANONYMOUSLY <-- default for everyone


in twig if you wanted to monitor access to different page sections:
{% if is_granted('ROLE_ADMIN') %}
    <h3>Admin Area</h3>
{% endif %}
