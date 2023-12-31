Feature: Product admin panel
    In order to maintain the products shown on the site
    As an admin
    I need to be able to add/edit/delete products

    Scenario: List available products
        Given there are 5 products
        And I am logged in as an admin
        And I am on "/admin"
        When I click "Products"
        Then I should see 5 products

    Scenario: Products show owner
        Given I am logged in as an admin
        And I author 5 products
        When I go to "/admin/products"
        # no products will be anonymous
        Then I should not see "Anonymous"

    @javascript
    Scenario: Add a new product
        Given I am on "/admin/products"
        And I am logged in as an admin
        When I click "New Product"
        And I fill in "Name" with "Veloci-chew toy"
        And I fill in "Price" with "20"
        And I fill in "Description" with "Have your velociraptor chew on this instead!"
        And I press "Save"
        Then I should see "Product created FTW!"
        And I should see "Veloci-chew toy"
        And I should not see "Anonymous"