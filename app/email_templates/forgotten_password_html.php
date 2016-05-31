<?php $this->layout('layouts/email_html', ['title' => 'Forgotten Password']) ?>

<h1 style="font-size: 20px;">Hi <?=$this->e($name)?>,</h1>

<p>
    We've received a request to reset your trk.life password. To complete this request, please click the link below:
</p>

<p>
    <a href="<?php echo $this->e($link); ?>"><?php echo $this->e($link); ?></a>
</p>

<p>
    If you believe this has been sent in error, please ignore this email.
</p>