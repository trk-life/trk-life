<?php $this->layout('layouts/email_text', ['title' => 'Forgotten Password']) ?>

Hi <?=$this->e($name)?>,

We've received a request to reset your trk.life password. To complete this request, please visit the link below:

<?php echo $this->e($link); ?>

If you believe this has been sent in error, please ignore this email.
