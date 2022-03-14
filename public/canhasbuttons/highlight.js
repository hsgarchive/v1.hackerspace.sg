function highlight(name)
{
  var text_val=eval("this." + name );
  text_val.focus();
  text_val.select();
}
