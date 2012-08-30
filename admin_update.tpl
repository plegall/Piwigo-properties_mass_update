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
</fieldset>

<fieldset>
  <legend>{'Options'|@translate}</legend>

  <p style="margin:0 0 15px 0;">
    <strong>{'Separator'|@translate}</strong>
    <br>
    <select name="separator">
      <option value="tab">{'tab'|@translate}</option>
      <option value="space">{'space'|@translate}</option>
      <option value="comma">, ({'comma'|@translate})</option>
      <option value="semicolon">; ({'semicolon'|@translate})</option>
    </select>
  </p>

  <p style="margin:0;">
    <strong>{'Property to update'|@translate}</strong>
    <br>
    <select name="property">
      <option value="name">{'Title'|@translate}</option>
      <option value="comment">{'Description'|@translate}</option>
      <option value="author">{'Author'|@translate}</option>
      <option value="tags">{'Tags'|@translate} {'(comma separated)'|@translate}</option>
    </select>
  </p>

</fieldset>

<p style="text-align:left">
<input class="submit" type="submit" name="validate" value="{'Submit'|@translate}">
</p>
</form>
