##  Thumbnail Resize & Crop em PHP
   
Função muito simples de resize e crop proporcional em PHP.
Funciona também com PNG/GIF transparente.

Para executar a função basta utilizar assim: 
 
     resize("original.png",$largura,$altura,$qualidade);

Se quiser colocar no HTML, basta utilizar assim:
	
	<img src="./thumbnail.php?img=original.png&w=120&h=550&q=100">