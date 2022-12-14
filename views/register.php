<?php
/**
 * @var \app\models\User $model
 * @var \app\core\View $this 
 */

$this->title = "Register";

use app\core\form\Form;
?>

<h1>Create an account</h1>

<?php $form = Form::begin("", "post"); ?>
    <div class="row">
        <div class="col">
            <?php echo $form->field($model, 'firstName') ?>
        </div>
        <div class="col">
            <?php echo $form->field($model, 'lastName') ?>
        </div>
    </div>
    
    <?php echo $form->field($model, "email"); ?>

    <div class="row">
        <div class="col">
            <?php echo $form->field($model, "password")->passwordField(); ?>
        </div>
        <div class="col">
            <?php echo $form->field($model, "confirmPassword")->passwordField(); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
<?php Form::end(); ?>
