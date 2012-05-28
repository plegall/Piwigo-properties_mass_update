<div class="titrePage">
  <h2>{'Send File - Photo Properties Mass Update'|@translate}</h2>
</div>

<form method="post" action="{$F_ACTION}" enctype="multipart/form-data">
<fieldset>
  <legend>{'Text file'|@translate}</legend>
  <p style="margin:0 0 15px 0; text-align:left;"><strong>{'File pattern'|@translate}</strong><br>
  img_001.jpg&lt;Tab&gt;{'Photo description, until the end of the line'|@translate}<br>
  IMG_002.JPG&lt;Tab&gt;{'Description for another photo'|@translate}
  </p>
<input type="file" name="update">
<p style="text-align:left">
<input class="submit" type="submit" name="validate" value="{'Submit'|@translate}">
</p>
</fieldset>
</form>
