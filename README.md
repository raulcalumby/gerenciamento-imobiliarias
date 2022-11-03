<h1> Projeto para Administração de imobiliária </h1> 

<p>A idéia do projeto é criar um sistema onde podetemos ter cadastros como Leads, Imóveis, Proprietários e Locatários, com acesso rapido as informações desses cadastros e filtragens, com isso</p>

<p>  Esse  projeto foi desenvolvido com PHP, TWIG para renderização de templates, JS, HTML E CSS  seguindo o padrão MVC com rotas.</p>

<h3>Como instalar ? </h3>
<h3>Primeiro Passo: </h3>
<p>Baixar o projeto dentro da pasta htdocs ou no  diretório ../../var/www/html/  caso utilize Linux , dentro do projeto digitar composer install</p>
<br>
<h3>Segundo Passo: </h3>
<p>Dentro do Projeto tem o arquivo DB, configure o arquivo Config.php com  o nome que você deu ao banco de dados, usuario e senha </p>
<br>
<h3>Terceiro Passo: </h3>

<p> É necessario apontar o servidor para o arquivo index.php por isso utilize essa configuração de exemplo em site-avaliable caso utilize LINUX ou no arquivo httpd-vhosts  do apache dentro do xaamp caso utilize WINDOWS </p>

<p> 

'<VirtualHost *:80> <br>
        DocumentRoot "C:\xampp\htdocs\NOME DO PROJETO \public"<br>
        ServerName bellintani.localhost <br>
        <Directory "C:\xampp\htdocs\bellintani-gestao\public"> <br>
        	Options FollowSymLinks Indexes <br>
		AllowOverride All <br>
             Require all granted <br>
	     Header set Access-Control-Allow-Origin "*" <br>
        </Directory> <br>
</VirtualHost>' 
</p>



<br>
<h3>Exemplo de Design :</h2>

<h3> Tela de Login </h3>
<img src='https://github.com/raulcalumby/gerenciamento-imobiliarias/blob/master/public/metronic/dist/assets/project-images/Login%20desktop.PNG' />

<h3>Exemplo de Listagem: </h3>
<img src='https://github.com/raulcalumby/gerenciamento-imobiliarias/blob/master/public/metronic/dist/assets/project-images/Locatario%20card%20List.PNG' />

<h3>Exemplo de cadastro no sistema </h3>
<img src='https://github.com/raulcalumby/gerenciamento-imobiliarias/blob/master/public/metronic/dist/assets/project-images/Locatario%20Card%20Add.PNG' />
