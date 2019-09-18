<?php
/* @var string $route */
/* @var string $class */
/* @var string $method */
/* @var bool $public */
/* @var string $returns */
?>

    '<?php echo $route ?>' => [
        'class' => <?php echo $class ?>::class,
        'method' => '<?php echo $method ?>',
<?php if($public) :?>
        'public' => true,
<?php endif; ?>
<?php if(isset($returns)) :?>
        'returns' => Returns::<?php echo $returns ?>,
<?php endif; ?>
    ],
