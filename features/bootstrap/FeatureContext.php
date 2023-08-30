<?php

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    private $currentUser = null;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }


    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term)
    {
        $searchBox = $this->assertSession()
            ->elementExists('css', 'input[name="searchTerm"]');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $button = $this->assertSession()
            ->elementExists('css', '#search_submit');

        $button->press();
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAnAdminUserWithPassword($username, $password)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->currentUser = $this->thereIsAnAdminUserWithPassword('admin','admin');
        $this->visitPath('/login');
        $this->getPage()->fillField('Username','admin');
        $this->getPage()->fillField('Password','admin');
        $this->getPage()->pressButton('Login');

    }

    /**
     * @Given there are :count products
     */
    public function thereAreProducts($count)
    {
        $this->createProducts($count);

    }

        /**
     * @Given I author :count products
     */
    public function iAuthorProducts($count)
    {
        $this->createProducts($count, $this->currentUser);
    }

    /**
     * @When I click :linkText
     */
    public function iClick($linkText)
    {
        $this->getPage()->clickLink($linkText);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts($count)
    {
        //$em = $this->getEm();
        //assertCount($count,$em->getRepository(Product::class)->findAll());
        $table = $this->getPage()->find('css','table.table');

        assertNotNull($table, 'No product table found');
        assertCount(intval($count), $table->findAll('css','tbody tr'));
    }

    private function createProducts($count, $author = null)
    {
        $em = $this->getEm();
        for ($i =0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Product '.$i);
            $product->setPrice(rand(10,1000));
            $product->setDescription('lorem');

            if ($author) {
                $product->setAuthor($author);
            };

            $em->persist($product);
        }

        $em->flush();
    }

    private function getEm(): EntityManager
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $purger->purge();
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }
}
