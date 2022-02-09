<?php

namespace Drupal\loremipsum\Controller;

use Drupal\Component\Utility\Html;

class LoremIpsumController {
   /**
   * Constructs Lorem ipsum text with arguments.
   * This callback is mapped to the path
   * 'loremipsum/generate/{paragraphs}/{phrases}'.
   * 
   * @param string $paragraphs
   *   The amount of paragraphs that need to be generated.
   * @param string $phrases
   *   The maximum amount of phrases that can be generated inside a paragraph.
   */
  
    public function generate($paragraphs, $phrases) {
        //Obtiene la configuración
        $config = \Drupal::config('loremipsum.settings');
        $page_title = $config->get('loremipsum.page_title');
        $source_text = $config->get('loremipsum.source_text');

        /* Repertory strategy.
        * TODO: different strategies:
        * - break on periods (like below)
        * - random words
        * - standard lorem ipsum with custom words thrown in
        * - begin with "Lorem Ipsum" etc.
        */


        /* Toma el texto fuente y lo separa en cuatro cadenas, 
        tomando como referencia para separar el salto de línea "\n".
        Cada cadena es una frase.  */
        $repertory = explode(PHP_EOL, $source_text);
        $element['#source_text'] = array();

        //Genera X párrafos hasta con Y frases, cada uno. Cada iteración es un párrafo.
        for ($i=1; $i<=$paragraphs; $i++) { 
            $this_paragraph = '';
            $random_phrases = mt_rand(round($phrases / 2), $phrases);

            $last_number = 0;
            $next_number = 0;

            for ($j=1; $j<=$random_phrases; $j++) { 
                /* Se escoge un número aleatorio entre 0 y 3 (los índices de las cadenas con las frases).
                Si el número escogido es el mismo que el último, se repite la selección aleatoria.
                Esto es para evitar repetir la misma frase. */
                do {
                    $next_number = floor(mt_rand(0, count($repertory) - 1));
                } while ($next_number === $last_number && count($repertory) > 1);
                
                //Se concatena al párrafo la frase escogida de manera aleatoria.
                $this_paragraph .= $repertory[$next_number] . ' ';
                $last_number = $next_number;
            }
            $element['#source_text'][] = Html::escape($this_paragraph);
        }
        $element['#title'] = Html::escape($page_title);
        $element['#theme'] = 'loremipsum';
        return $element;
    }
}