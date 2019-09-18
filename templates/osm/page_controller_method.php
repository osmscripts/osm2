<?php
/* @var string $method Controller method name which renders the page */
/* @var string $layer Layer name dedicated to this page */
?>

    public function <?php echo $method ?>() {
        return osm_layout('<?php echo $layer ?>', [
            '#page' => [
                'title' => osm_t("[page title]"),
            ],
            // bind data to views
        ]);
    }
