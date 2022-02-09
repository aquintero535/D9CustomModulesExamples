<?php

namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("Hello block"),
 *   category = @Translation("Hello World"),
 * )
 */
class HelloBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    //build construye o renderiza el bloque en la página.
    public function build() {
        $config = $this->getConfiguration(); //Obtiene la configuración.
        //Si no está vacía la configuración, obtén el nombre. Si está vacía, coloca el nombre como 'a nadie'.
        if (!empty($config['hello_block_name'])) {
            $name = $config['hello_block_name'];
        }
        else {
            $name = $this->t('to no one');
        }
        //Regresa un arreglo con los datos a renderizar en el bloque.
        return array(
            '#markup' => $this->t('Hello @name!', array(
                '@name' => $name,
                )
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    // Función que crea los formularios, o sea, las opciones del bloque para que sean modificadas por el usuario.
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $default_config = \Drupal::config('hello_world.settings');
        $config = $this->getConfiguration();

        //Cada item en el arreglo asociativo de $form es un formulario.
        $form['hello_block_name'] = array (
            '#type' => 'textfield',
            '#title' => $this->t('Who'),
            '#description' => $this->t('Who do you want to say hello to?'),
            '#default_value' => isset($config['hello_block_name']) ? $config['hello_block_name'] : $default_config->get('hello.name'),
        );

        $form['hello_block_prueba'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Prueba'),
            '#description' => $this->t('Formulario de prueba'),
            '#default_value' => $default_config->get('hello.name'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        $default_config = \Drupal::config('hello_world.settings');
        return [
            'hello_block_name' => $default_config->get('hello.name'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    //Valida que la información introducida en el formulario sea correcta.
    public function blockValidate($form, FormStateInterface $form_state)
    {
        if ($form_state->getValue('hello_block_name') === 'John') {
            $form_state->setErrorByName('hello_block_name', $this->t('You can not say hello to John.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    //Guarda la configuración del formulario.
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->setConfigurationValue('hello_block_name', $form_state->getValue('hello_block_name'));
    }
}