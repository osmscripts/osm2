<?php
/* @var string $method Controller method name which renders the page */
/* @var string $module Module name */
/* @var string $view Blade template which renders the Mustache template */
?>

    public function <?php echo $method ?>() {
        return View::new(['template' => '<?php echo $module ?>.<?php echo $view ?>']);
    }
