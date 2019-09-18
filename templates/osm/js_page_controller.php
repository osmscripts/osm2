<?php
/* @var string $class Class name */
?>
import Controller from "Osm_Framework_Js/Controller";

export default class <?php echo $class ?> extends Controller {
    get events() {
        return Object.assign({}, super.events, {
            // handle JS events here
        });
    }
};