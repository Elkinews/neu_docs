<link href="{{ captcha_layout_stylesheet_url() }}" type="text/css" rel="stylesheet">
<form action="{{ url('/doverifycaptcha') }}" method="POST"  id="captcha_form">
  {!! csrf_field() !!}
  {!! captcha_image_html('NdeCaptcha') !!}

  <label for="CaptchaCode" class='CaptchaCode_label'>Enter the code below and press continue:</label>
  <input type="text" id="CaptchaCode" name="CaptchaCode" >
  <!-- <button type="submit">Submit</button> -->

</form>

<style>
  body{
    margin: 0;
  }
  *{
    font-family: "Poppins", sans-serif !important;
  }
  #CaptchaCode{
    width: 100%;
    
    padding: 8px;
    height: 56px;
  }

  .CaptchaCode_label{
    margin: 20px 0 10px;
  }
</style>