<?php

namespace Drupal\Tests\loremipsum\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests for the Lorem Ipsum module.
 *
 * @group loremipsum
 */
class LoremIpsumTests extends BrowserTestBase {
    /**
     * Modules to install.
     * 
     * @var array
     */
     protected static $modules = array('loremipsum');


    protected $defaultTheme = 'stable';

    /**
     * A simple user.
     * 
     * @var \Drupal\user\Entity\User
     */
    private $user;


    /**
     * Perform initial setup tasks that run before every test method.
     */
    public function setUp() {
        parent::setUp();
        $this->user = $this->drupalCreateUser(array(
            'administer site configuration',
            'generate lorem ipsum',
        ));
    }

    /**
     * Tests that the Lorem ipsum page can be reached.
     */
    public function testLoremIpsumPageExists() {
        // Login.
        $this->drupalLogin($this->user);

        //Generator test:
        $this->drupalGet('loremipsum/generate/4/20');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Tests the config form.
     */
    public function testConfigForm() {
        // Login.
        $this->drupalLogin($this->user);

        //Access config page.
        $this->drupalGet('admin/config/development/loremipsum');
        $this->assertSession()->statusCodeEquals(200);

        //Test the form elements exist and have defaults.
        $config = $this->config('loremipsum.settings');
        $this->assertSession()->fieldValueEquals(
            'page_title',
            $config->get('loremipsum.page_title'),
        );
        $this->assertSession()->fieldValueEquals(
            'source_text',
            $config->get('loremipsum.source_text'),
        );

        //Test form submission.
        $this->drupalPostForm(NULL, array(
            'page_title' => 'Test lorem ipsum',
            'source_text' => 'Test phrase 1 \nTest phrase 2 \nTest phrase 3 \n',
        ), t('Save configuration'));
        $this->assertSession()->pageTextContains('The configuration options have been saved.');
        
        //Test the new values are there.
        $this->drupalGet('admin/config/development/loremipsum');
        $this->assertSession()->statusCodeEquals(200);
        $this->assertSession()->fieldValueEquals(
            'page_title',
            'Test lorem ipsum',
        );
        $this->assertSession()->fieldValueEquals(
            'source_text',
            'Test phrase 1 \nTest phrase 2 \nTest phrase 3 \n',
        );
    }
}