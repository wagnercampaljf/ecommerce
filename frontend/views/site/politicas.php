<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 23/02/2015
 * Time: 11:26
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Políticas Peça Agora';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);

$this->params['breadcrumbs'][] = $this->title;
$entrega = "<h2>Política de Entrega</h2>

<b>1-	Condições gerais:</b>

<p>As entregas feitas pelos vendedores são realizadas de segunda à sexta das 8h
às 18h e sábados das 8h às 12h. As realizadas pelos Correios são feitas de
segunda à sexta das 8h às 18h.</p>
<p>O prazo de entrega é contado a partir do dia útil a seguir da data de confirmação
do pagamento, em dias úteis, excetuando-se sábados, domingos e feriados.
Após a confirmação do pedido não é possível alterar o local de entrega.
As entregas dos produtos são de inteira responsabilidade dos vendedores,
cabendo ao Peça Agora apenas suporte e repasse de eventuais problemas e o
acompanhamento das entregas.</p>
<p>São feitas até 03(três) tentativas de entrega no local informado no pedido. É
obrigatório que, no endereço citado, esteja uma pessoa autorizada pelo usuário
comprador com idade maior de 18 anos e portando documento de identificação
para receber a mercadoria e assinar o protocolo.</p>
<p>Caso haja três tentativas de entrega sem sucesso, o pedido voltará para o
vendedor responsável sendo retido o valor do frete. Novas tentativas serão feitas
apenas mediante confirmação do endereço, bem como pagamento de novo
frete. O novo prazo para entrega será o mesmo informado no pedido e conta a
partir do dia útil a seguir da data de confirmação do endereço.</p>
<p>Em caso de não reclamação do pedido, bem como de não confirmação do
endereço será iniciado o processo de cancelamento e ocorrerá o estorno do
pedido. Para mais informações sobre a forma de devolução do dinheiro, consulte
a nossa política de troca e devoluções.</p>
<p>O responsável pelo custo do frete é o usuário comprador, sendo este valor
adicionado ao valor total da compra, sendo os vendedores responsáveis por
pagar o frete quando for enviar o produto.</p>
<p>Os prazos de entrega são contados mediante considerações sobre o estoque,
região, tempo de preparo do produto e data de emissão da nota fiscal.</p>

<b>2-	Atrasos na entrega:</b>

<p>Fatores adversos como chuvas fortes, enchentes, greves, acidentes ou qualquer
outro caso fortuito ou força maior que impossibilite a chegada do transportador
no local indicado poderá acarretar em atrasos na entrega do produto.</p>
<p>Os vendedores são inteiramente responsáveis pelas entregas realizadas com
atraso.</p>

<b>3-	Recusar a entrega:</b>

<p>Caso ocorra alguma das situações expostas abaixo, a entrega deverá ser
recusada e o motivo deverá ser exposto no verso do DANFE (Documento Auxiliar
da Nota Fiscal Eletrônica):

<ul>
    <li>Ausência de acessórios;</li>
    <li>Produto com avarias;</li>
    <li>Embalagem violada;</li>
    <li>Produto diferente do pedido.</li>
</ul>
Caso o pedido seja aceito, o ocorrido deverá ser comunicado ao SAC através do
email sac@pecaagora.com no prazo de até 07 dias corridos a partir da data de
entrega.</p>

<b>4-	Compromisso das transportadoras:</b>

<p>As transportadoras têm o compromisso de efetuar as 02 (duas) tentativas de
entrega no endereço informado pelo cliente.
O Peça Agora não autoriza as transportadoras a:
    <ul>
        <li>Entrar no domicílio;</li>
        <li>Fazer montagem e manutenção dos produtos;</li>
        <li>Abrir embalagens;</li>
        <li>Realizar entregas por meios alternativos (Ex. Içar pela janela)</li>
        <li>Executar a entrega a menores de 18 (dezoito) anos sem documento de identificação;</li>
        <li>Executar a entrega em outro documento que não consta no DANFE.</li>
    </ul>
</p>
<b>5-	Entrega Própria:</b>

<p>A Entrega Própria é disponível para apenas alguns produtos de alguns
vendedores dispostos no site. O vendedor estabelece uma localização onde
consegue realizar a entrega de forma mais rápida.</p><p>
O prazo estipulado para que ocorra a entrega é dado pelo vendedor e contará a
partir do dia útil a seguir da data de confirmação do pagamento, ressalvado
motivos de força maior.</p><p>
O Peça Agora não se responsabiliza por atrasos na entrega dos produtos e por
eventuais prejuízos relacionado aos atrasos.</p><p>
O vendedor é responsável por definir o peso máximo, raio de quilometragem e
se haverá cobrança pela entrega.</p><p>
A Entrega Própria está disponível apenas para algumas regiões. Confira se o
serviço está disponível para o seu CEP no momento da compra.</p>

<b>6-	Foro de eleição:</b>

<p>Para dirimir eventuais controvérsias referentes à presente Política de entregas,
fica instituído o foro da Comarca da cidade de Juiz de Fora no estado de Minas
Gerais.</p>

<b>7-	Contato:</b>

<p>Para mais dúvidas ou informações não explicitadas no presente documento, o
interessado poderá entrar em contato pelo telefone (32) 3015-0023 ou pelo email
sac@pecaagora.com.</p>

";

$trocas = "<h2>Política de troca e devoluções</h2>

<b>1-	Condições Gerais:</b>

<p>O pedido para realização das trocas e devoluções devem ocorrer em até 07 dias
corridos, a contar a partir da data da entrega do produto. As ocorrências devem
ser formalizadas e comunicadas ao SAC do Peça Agora através do email
sac@pecaagora.com.</p><p>
As despesas decorrentes da postagem do produto por troca ou devolução serão
custeadas pelos vendedores responsáveis pelo pedido.</p><p>
O Peça Agora e os vendedores se isentam da obrigação da troca ou devolução
dos produtos que não preencherem os requisitos do presente documento.</p><p>
As trocas e devoluções dos produtos são de inteira responsabilidade dos
vendedores, cabendo ao Peça Agora apenas o suporte inicial, o repasse da
intenção ao vendedor e o acompanhamento do processo.</p>

<b>2-	Para recusar o produto:</b>

<p>Caso o produto seja entregue com algumas das características abaixo, o pedido
deverá ser recusado e o motivo da recusa deverá ser exposto no verso do
DANFE (Documento Auxiliar da Nota Fiscal Eletrônica) no ato da entrega, seja
este causado pelo transporte ou por inconformidade no produto. Alguns
exemplos encontram-se abaixo:
<ul>
    <li>Ausência de acessórios;</li>
    <li>Produto com avarias;</li>
    <li>Embalagem violada;</li>
    <li>Produto diferente do pedido.</li>
</ul>
Caso o pedido seja aceito, deverá ser comunicado ao SAC do Peça Agora
através do email sac@pecaagora.com no prazo de até 07 dias corridos a partir
da data de entrega.</p><p>
Em caso de devolução ou troca do produto ocorra por responsabilidade do
vendedor, este tem o dever de retirar o produto e assumir automaticamente o
valor do frete.</p><p>
Em caso de danos no produto ocorra por responsabilidade da transportadora,
esta tem o dever de ressarcir o valor do produto e frete de troca. O vendedor
deve enviar outro produto ao cliente, enquanto aguarda o ressarcimento do valor
da transportadora.
</p>
<b>3-	Condições para a troca e devoluções:</b>

<p>Só serão aceitos os produtos que atenderem as seguintes condições:
<ul>
    <li>Tenha sido comunicado a intenção ao SAC do Peça Agora em até 07
dias corridos a partir da data de entrega;</li>
    <li>Produtos sem avarias provenientes do uso;</li>
    <li>Produtos acompanhados dos acessórios caso existam;</li>
    <li>Produtos que não apresentem desgastes devido ao uso.</li>
</ul>
</p>
<b>4-	Direito de arrependimento:</b>

<p>O Peça Agora em conformidade com o Código de Defesa do Consumidor,
garante o direito de arrependimento da compra.</p><p>
Os custos referentes ao processo de devolução e transporte, são de
responsabilidade do vendedor. O Peça Agora não se responsabiliza pelos custos
relativos ao transporte e a devolução da mercadoria. Para a devolução, o cliente
deverá comunicar ao SAC do Peça Agora através do email sac@pecaagora.com
a intenção e especificar que está exercendo este direito de arrependimento
especificadamente, informação que será repassada ao vendedor responsável.</p><p>
O produto deverá ser devolvido na embalagem original, com todos os acessórios
caso existam, e acompanhado do DANFE (Documento Auxiliar da Nota Fiscal
Eletrônica) como motivo da recusa pelo Direito de arrependimento exposto no
verso.</p><p>
É dever do vendedor entrar em contato com a empresa administradora do
pagamento, MoIP Pagamentos S.A e solicitar o cancelamento da transação e a
devolução da quantia paga pelo cliente.</p>

<b>5-	Formas de devolução:</b>

<b>5.1 – Devolução pelos Correios:</b>

<p>Para que o produto possa ser postado pelos Correios, o conjunto produto e
embalagem devem atender às seguintes especificações:
<ul>
    <li>Peso máximo de 30 Kg;</li>
    <li>Altura mínima de 2 cm e máxima de 105 cm;</li>
    <li>Largura mínima de 11 cm e máxima de 105 cm;</li>
    <li>Comprimento mínimo de 16 cm e máximo de 105 cm;</li>
    <li>Soma máxima das dimensões (Altura + Largura + Comprimento) de 200 cm.</li>
</ul>
Será enviado pelos atendentes Peça Agora por e-mail, o código de postagem
que deverá ser apresentado junto aos Correios. Para a postagem, o produto
deverá estar em sua embalagem de origem, com todos os acessórios caso
existam, e acompanhado do DANFE, onde deverá estar explicitado no verso o
motivo da devolução. Todas as instruções estão definidas na Política de Trocas
e Devoluções.
</p>
<b>5.2 – Devolução por coleta no local de entrega:</b>

<p>Os vendedores poderão coletar os produtos nos locais de entrega. As coletas
serão feitas de segunda à sexta feira das 8h às 18h. Caso a devolução seja feita
por transportadora, a mesma deve deixar no local um comprovante da coleta. O
horário bem como o modelo da documentação podem sofrer alterações de
acordo com a transportadora utilizada.</p><p>
O produto deverá estar em sua embalagem de origem, com todos os acessórios
caso existam, e acompanhado do DANFE, onde deverá estar explicitado no
verso o motivo da devolução.</p><p>
ATENÇÃO: Não deverão ser entregues os produtos para as transportadoras que
não apresentarem o protocolo com o pedido.</p>

<b>6-	Prazo para a resolução da troca:</b>

<p>O vendedor tem um prazo de até 30 (trinta) dias corridos para a resolução da
troca a partir da data de recebimento do produto pelo vendedor. Caso o pedido
atenda aos critérios determinados pela atual política de troca e devoluções, será
enviado novo produto ao cliente. Os custos decorrentes da postagem são de
responsabilidade do vendedor.</p>

<b>7-	Devolução do dinheiro:</b>

<p>Em caso de estorno é devolvido o valor integral pago pelo usuário comprador,
incluindo as tarifas de transação.</p><p>
É dever do vendedor entrar em contato com a empresa administradora do
pagamento, MoIP Pagamentos S.A e solicitar o cancelamento da transação e a
devolução da quantia paga pelo cliente.</p><p>
Para compras efetuadas via cartão de crédito, o MoIP Pagamentos S.A será
notificado e o estorno ocorrerá na fatura seguinte ou na posterior em forma de
crédito. O prazo de ressarcimento e a cobrança das parcelas remanescentes são
de responsabilidade do MoIP Pagamentos S.A. Em caso de transações
parceladas, cada operadora segue um fluxo diferente, podendo o estorno ser
parcial ou valor total da transação.</p><p>
Para compras efetuadas com boleto bancário, o usuário comprador deverá criar
uma conta MoIp para compradores utilizando o mesmo e-mail utilizado na
compra, o valor será creditado na conta MoIp e o usuário comprador solicitará a
transferência para sua conta bancária.</p>

<b>8- Foro de eleição:</b>
<p>Para dirimir eventuais controvérsias referentes à presente Política de Trocas de
devoluções, fica instituído o foro da Comarca da cidade de Juiz de Fora no
estado de Minas Gerais.</p>

<b>9- Contato:</b>
<p>Para mais dúvidas ou informações não explicitadas no presente documento, o
interessado poderá entrar em contato pelo telefone (32) 3015-0023 ou pelo email
sac@pecaagora.com.</p>
";

$pagamento = "<h2>Política de pagamento e frete</h2>

<b>1-	Condições gerais:</b>
<p>Para a realização do pagamento o Peça Agora utiliza como intermediador
financeiro o MoIP Pagamentos S.A. inscrito no CNPJ sob o número
08.718.431/0001-08. São aceitas as seguintes formas:

Pagamento à vista:</p>
<ul>
    <li>Boleto Bancário;</li>
    <li>Cartão de crédito;</li>
    <li>Transferência bancária.</li>
</ul>

Pagamento parcelado:
<ul>
    <li>Cartão de crédito. </li>
</ul>

<b>2-	Pagamentos com cartão de crédito:</b>

<p>Para compras realizadas com cartão de crédito, os pedidos são sujeitos à
confirmação de pagamento pela administradora do cartão após a finalização do
pedido. Somente após a confirmação do pagamento que será prosseguido o
envio da mercadoria.</p><p>
Os pedidos que ultrapassem o limite dos cartões de crédito poderão ser
cancelados pela administradora do cartão.</p><p>
Não são possíveis alterar os dados da conta e do cartão de crédito após a
finalização do pedido.</p>

<b>3-	 Pagamentos com boleto bancário:</b>

<p>Após a finalização do pedido, o boleto deverá ser pago em qualquer agência
bancária de sua preferência ou pelo Internet Banking, até a data de vencimento
que consta no boleto. Após o pagamento, o banco tem o prazo de até 3 (três)
dias úteis para a confirmação do pagamento. Os prazos de entrega são contados
a partir do dia útil a seguir da data de confirmação do pagamento pelo banco.
O não pagamento do boleto até a data de vencimento implica o cancelamento
deste e do pedido, sem qualquer custo para o cliente.</p><p>
Para solicitar a segunda via do boleto, este deve estar na validade. O cliente
poderá acessar nos Meus Pedidos em Minha Conta, e clicar no botão “Solicitar
2ª via de boleto”.</p><p>
Não são possíveis alterar os dados do pagamento após a finalização do pedido.
A ausência de pagamento até a data de vencimento implica no cancelamento da
compra e do boleto. Dessa forma o cliente deverá refazer a compra. Todas essas
ações são feitas de forma rápida e segura.</p>

<b>4-   Pagamentos com Transferência Bancária:</b>

<p>Após o pagamento por transferência bancária, o banco fará a compensação
bancária para confirmação. Os prazos de entrega são contados a partir do dia
útil a seguir da data de confirmação do pagamento pelo banco.</p>

<b>5-	Parcelamento das compras:</b>

<p>As compras poderão ser pagas em até 12 prestações nos cartões de crédito. Os
valores dos juros incidentes sobre as parcelas serão informados na finalização
do pedido, na etapa de pagamento.
</p>

<b>6-	Pagamento em duplicidade:</b>

<p>Caso o valor do pedido seja cobrado em duplicidade, deve-se entrar em contato
com o SAC através do sac@pecaagora.com onde serão repassadas as
informações para o procedimento do reembolso.</p>

<b>7-	Frete:</b>
<p>O cálculo do frete é baseado mediante a localização do cliente. Dessa forma,
alterações no local de entrega alterará o valor cobrado pelos Correios e
transportadoras.</p><p>
O valor do frete pode sofrer alterações durante a finalização do pedido, devido
ao volume das mercadorias e à localização do endereço para entrega.</p><p>
O frete do pedido pode ser calculado na página do produto. Caso o cliente tenha
feito o login, essa etapa será feita automaticamente, com a inserção do CEP
cadastrado. Caso o cliente não seja reconhecido, essa etapa é manual.</p><p>
O valor do frete poderá sofrer mudanças mediante alterações na quantidade de
produtos e peso do pedido, sendo o valor correto encontrado no carrinho no
momento do pagamento, onde todas as variações serão concluídas e
consideradas pelos parceiros para efetivação da compra e venda.</p><p>
O frete grátis está disponível para apenas alguns vendedores, onde cada
vendedor define o local de abrangência e os produtos.</p>

<b>8-   Foro de eleição:</b>
<p>Para dirimir eventuais controvérsias referentes à presente Política de
pagamento, fica instituído o foro da Comarca da cidade de Juiz de Fora no
estado de Minas Gerais.</p>

<b>9-   Contato:</b>
<p>Para mais dúvidas ou informações não explicitadas no presente documento, o
interessado poderá entrar em contato pelo telefone (32) 3015-0023 ou pelo email
sac@pecaagora.com.</p>
";

$privacidade = "<h2>Política de privacidade</h2>
<b>1– Considerações gerais:</b>
<p>O Peça Agora, sabe da importância de resguardar e manter a confidencialidade
das informações cedidas pelos clientes. Para isso, o presente documento tem
como o objetivo esclarecer quais informações dos consumidores são reunidas e
como estas são utilizadas. Este documento aplica-se a todos os produtos e
serviços oferecidos pelo Peça Agora, e rege a forma como captar, manipular e
divulgar as informações dos usuários.</p>

<b>2 – Recolhimento de informações:</b>
<p>2.1 – Informações de cadastro: As informações solicitadas são nome completo,
telefone, e-mail, CPF, endereço, bairro, CEP, cidade, estado, quando se trata de
pessoa física e nome, CPF e telefone do representante, CNPJ, razão social,
nome fantasia da empresa, e-mail, endereço, bairro, cidade e estado quando se
trata de pessoa jurídica, dentre outras informações que o Peça Agora julgar
necessária.</p><p>
2.2 – Dados de navegação: Ao acessar o site, o sistema insere um Cookie no
seu navegador. São coletados o endereço de IP, localização geográfica, fonte
de referência, tipo de navegador, duração da visita, páginas visitadas, dentre
outras informações.</p>

<b>3 – Utilização dos dados:</b>
<p>Os dados informados durante o cadastro são utilizados exclusivamente para a
identificação do usuário no site e para as etapas de entrega dos produtos.</p><p>
Os e-mails são utilizados para o envio de confirmação de cadastro, alteração de
senha, atualização de status de pedido, confirmação de pagamento, carrinho
abandonado, prazo de confirmação de cadastro, avaliação de pós venda e prazo
de atualização de estoque. Os e-mails serão utilizados também para o envio de
promoções, novidades e informações do Peça Agora.</p><p>
O nome e a cidade do usuário serão divulgados quando o cliente fizer uma
avaliação dos produtos e/ou vendedores.</p><p>
Os “Cookies” e os dados de navegação são utilizados com o objetivo de traçar
um perfil do público e aperfeiçoar os produtos, conteúdos e serviços oferecidos
pelo Peça Agora.</p>

<b>4 – Compartilhamento de informações:</b>
<p>Os dados dos clientes não são vendidos, trocados ou divulgados para terceiros.
As informações poderão ser compartilhadas, quando houver requerimento das
autoridades competentes, mediante ordem judicial, em casos que tratem de
investigação de caráter pessoal ou situações que possam resultar em crimes em
desfavor dos usuários ou do Peça Agora.</p><p>
As informações que não são de caráter individual poderão ser compartilhadas
com parceiros Peça Agora sem prévia autorização do usuário, com o objetivo de
melhorar os produtos e serviços oferecidos pelo Peça Agora.</p>

<b>5 – Transações financeiras:</b>
<p>Para as transações financeiras é utilizado o serviço do MoIP Pagamentos S.A.
inscrito no CNPJ sob o número 08.718.431/0001-08, onde os dados são
repassados automaticamente. Estes serão usados apenas para transações
financeiras, e é proibido, por parte do MoIP Pagamento S.A, o uso das
informações para quaisquer outras finalidades.</p>

<b>6 – Segurança das informações:</b>
<p>Os dados são registrados pelo Peça Agora de forma automatizada, o que
dispensa a manipulação humana.</p><p>
Todos os dados pessoais coletados no site são armazenados em um banco de
dados com acesso restrito a pessoas treinadas que são obrigadas por contrato
a manter a confidencialidade das informações.</p><p>
Não são armazenados nem utilizados dados relacionados a cartões de crédito,
como números, datas de validade, códigos de segurança, etc. Como dito
anteriormente, estes são repassados automaticamente para as instituições
financeiras e/ou empresas de pagamento eletrônico que são parceiras do Peça
Agora.</p><p>
Para promover a segurança dos dados, pede-se para que estes não sejam
divulgados a terceiros.</p><p>
O site sofrerá constantes alterações com o objetivo de corrigir imprecisões, erros
e falhas no sistema. Durante este processo, as informações dos usuários não
serão divulgadas a terceiros.</p><p>
Por motivo de segurança, é proibida por parte dos operadores a alteração dos
dados cadastrais, endereços de entrega, ou quaisquer informações dos
usuários.</p>

<b>7 – Redes sociais:</b>
<p>Ao clicar nos botões de compartilhamento em redes sociais disponíveis nas
páginas do Peça Agora, o usuário publicará o conteúdo através de seu perfil.
O Peça Agora não publica conteúdo em redes sociais e não envia e-mails em
nome de seus clientes sem autorização prévia e não tem acesso às informações
de login dos clientes nessas redes.</p>

<b>8 – Mudanças na Política de privacidade:</b>
<p>O presente documento poderá sofrer atualizações. Toda e qualquer alteração
será informada nesta página, sendo válida apenas a última versão.</p><p>
Reservamo-nos no direito de alterar a Política de Privacidade a qualquer
momento.
Para utilizar informações não mencionadas nessa Política de Privacidade,
solicitaremos sua autorização.</p>

<b>9 – Foro de eleição:</b>
<p>Para dirimir eventuais controvérsias referentes à presente Política de
Privacidade, fica instituído o foro da Comarca da cidade de Juiz de Fora no
estado de Minas Gerais.</p>

<b>10 – Contato:</b>
<p>Para mais dúvidas ou informações não explicitadas no presente documento, o
interessado poderá entrar em contato pelo telefone (32) 3015-0023 ou pelo email
sac@pecaagora.com.</p>




";

$termos = "<h2>Termos e condições de uso</h2>
<b>1-	Condições Gerais:</b>

<p>OPT Soluções LTDA, inscrita no CNPJ sob o nº 18.947.338/0001-10, sediada na
cidade de Juiz de Fora – Minas Gerais na Rua José Lourenço Kelmer, S/N,
Campus Universitário, São Pedro, CEP: 36036-900, única exclusiva proprietária
dos domínios da marca “Peça Agora”, como “www.pecaagora.com”, denominado
site Peça Agora, estabelece o presente Termo e Condições de Uso conforme as
condições abaixo.</p><p>
O presente documento tem como objetivo estabelecer as condições gerais de
compra e venda de produtos no que tange os direitos e deveres relativos ao uso
do site e do relacionamento com os clientes.</p><p>
O presente documento se aplica aos interessados na figura de usuários
compradores, vendedores e anunciantes quanto à utilização do site e redes
sociais de domínio do Peça Agora.</p><p>
O uso das funcionalidades do site implica em concordância total ao presente
Termos e Condições de Uso vigente no momento do acesso, bem como à
política de privacidade e as demais políticas do site Peça Agora.</p>

<b>2-	Serviços oferecidos:</b>

<p>
Os serviços do site Peça Agora consistem em:
<ol type='i'>
    <li>Ofertar espaços e viabilizar a comercialização de peças para
reposição entre vendedores e usuários compradores;</li>
    <li>Promover um espaço para gestão das compras das frotas de forma
eficiente, propiciando aos usuários compradores e vendedores maior
competitividade no mercado;</li>
    <li>Ofertar espaços para que empresas e instituições do ramo de peças,
possam fazer anúncios e promoções de seus produtos.</li>
</ol>
Deve-se ressaltar que o serviço oferecido pelo Peça Agora se relaciona
apenas à intermediação para comercialização online de peças para
equipamentos, não abrangendo fabricação, preparação, disponibilização e
entrega dos produtos, sendo estes itens de inteira responsabilidade dos
vendedores.
</p>
<b>
3-	Do cadastro:
</b>
<p>
O usuário comprador, ao realizar o cadastro no site deverá declarar todas as
informações pedidas e afirmar ser responsável pela exatidão das
informações cedidas. Estas informações poderão ser verificadas pelo
“www.pecaagora.com“.</p><p>
O Peça Agora se declara possuidor do direito de impedir a realização de
novos cadastros e de cancelar cadastros já existentes no caso de
informações incorretas, não confirmadas ou que apresentem qualquer
anomalia que demostrem tentativas de fraudar o sistema ou que represente
perigo para a segurança das informações de outros usuários ou do próprio
Peça Agora.</p><p>
Ao finalizar o cadastro, o usuário terá acesso às funcionalidades do site a
partir de login e senha. O usuário é inteiramente responsável pela cessão
dessas informações a terceiros e as implicações que decorrerão desse ato.
</p>
<b>
4-	Dos produtos:
</b>
<p>No Peça Agora os vendedores poderão expor seus produtos nas respectivas
categorias e subcategorias. A exposição contará com as especificações e o
preço do produto.</p><p>
A disponibilidade e a qualidade dos produtos são de inteira responsabilidade
dos vendedores.</p><p>
Os produtos, serão passíveis de avaliações dos usuários, no que tange à
qualidade, usabilidade, vida útil, dentre outras características que sejam
relevantes para os usuários sem que essa ação seja questionada pelos
fabricantes e vendedores.</p>
<b>
5-	Obrigações dos usuários:
</b>
<p>É de obrigação do usuário fornecer todas as informações do cadastro de
forma verídica, se responsabilizando integralmente por todas as informações
cedidas.</p><p>
É de inteira responsabilidade do usuário a disponibilização de suas
informações para terceiros, mesmo que estes sejam locais e sites que
tenham sido acessados através do Peça Agora.</p><p>
Os dados de login e senha são de inteira responsabilidade dos usuários. O
Peça Agora não se responsabiliza por compras realizadas por terceiros em
nome de usuário compradores cadastrados.</p><p>
O usuário se obriga a efetuar o pagamento total dos produtos por ele
encomendados e devidamente entregues pelo vendedor.</p>
<b>
6-	Obrigações dos vendedores:
</b>
<p>Os vendedores se responsabilizam pela veracidade das informações cedidas
no cadastro e na exposição de produtos, bem como garante a disponibilidade
dos produtos para a venda no site.</p><p>
Os vendedores se obrigam a emitir a nota fiscal dos produtos vendidos e
entregar aos usuários compradores no devido prazo e nas condições ideais
para uso.</p><p>
Os vendedores asseguram que detém todos os direitos sob a publicação e
comercialização dos produtos expostos no site.
Os vendedores se responsabilizam pelos preços e/ou prazos inseridos de
forma equivocada, bem como os preços e prazos anunciados em promoções
caso ocorra algum erro e a venda de produtos conclua-se com os preços
alterados.</p><p>
Os vendedores se responsabilizam, a efetuar o pagamento das tarifas sobre
os produtos vendidos ao Peça Agora até o dia estipulado no contrato. Caso
não seja verificado o pagamento, o vendedor será inabilitado de expor os
produtos. Essa situação perdurará enquanto não for detectado o pagamento
e será incidido uma multa sobre o período de inadimplência.</p><p>
O vendedor autoriza a avaliação dos usuários no que tange à qualidade dos
produtos, agilidade no atendimento, entrega e suporte.</p>
<b>
7-	Obrigações dos anunciantes:
</b>
<p>Os anunciantes se comprometem a efetuar o pagamento, em sua totalidade,
das publicações, dos anúncios e de todos os serviços realizados pelo Peça
Agora. Caso não seja verificado o pagamento, o anunciante será inabilitado
de expor suas publicações. Essa situação perdurará enquanto não for
detectado o pagamento e será incidido uma multa sobre o período de
inadimplência.</p><p>
O anunciante se declara possuidor dos direitos de publicação e divulgação
dos materiais publicados no Peça Agora, sendo inteiramente responsável
pela veracidade das informações anunciadas.</p>
<b>
8-	Obrigações do “www.pecaagora.com”:
</b>
<p>O “www.pecaagora.com” se compromete a manter o sigilo das informações
cedidas pelos usuários no que tange aos dados cadastrais, dados de login e
senha, bem como os valores das operações financeiras decorrentes dos
serviços oferecidos no presente documento.</p><p>
O www.pecaagora.com se responsabiliza a disponibilizar um espaço virtual
para que os usuários possam efetuar as compras de peças de equipamentos,
bem como disponibilizar ao usuário meios de pagamento online.</p>
<b>
9-	Das informações e privacidade:
</b>
<p>O usuário aceita ser identificado no site através de “cookies” e outras
tecnologias. Essa ação propicia a melhorias dos serviços prestados pelo
Peça Agora.</p><p>
As informações captadas dos clientes são armazenadas em ambientes
seguros, onde são manipuladas por operadores treinados.</p><p>
O Peça Agora toma todas as medidas para manter a segurança e a
confidencialidade das informações dos usuários. Porém, o site não se
responsabiliza pela perda ou divulgação das informações confidenciais dos
usuários por meio da invasão do sistema por terceiros.</p><p>
Para mais informações sobre a coleta, manipulação e divulgação dos dados,
consulte a nossa política de privacidade.</p>
<b>
10-	Das vedações:
</b>
<p>
É proibida qualquer ação explicitada abaixo, por parte dos usuários, vendedores e anunciantes:
<ol type='i'>
    <li>Venda de qualquer produto que não esteja relacionado ao mercado de peças para reposição de equipamentos, bem como a atividades fins do site;</li>
    <li>Divulgação de dados pessoais de clientes/compradores;</li>
    <li>Interferir e prejudicar na negociação de outros vendedores;</li>
    <li>Publicação de conteúdo, imagens ou qualquer material que possa desrespeitar ou prejudicar terceiros;</li>
</ol>
Todos os comportamentos expostos acima, e outros que se enquadrem em
condutas desrespeitosas, são passíveis de suspensão e proibição do uso do
site, bem como, caso seja necessário, a utilização das medidas judiciais
cabíveis.
</p>
<b>
11-	Limitação das responsabilidades:
</b>
<p>
O Peça Agora não efetua quaisquer operações de compra e venda dos
produtos anunciados no site, desempenhando o papel de intermediário da
relação, sendo essas ações exclusivas dos usuários, vendedores e
anunciantes.</p><p>
O Peça Agora não assume quaisquer responsabilidades referentes às
relações comerciais entre os usuários, vendedores e anunciantes, bem como
as obrigações tributárias, danos e prejuízos decorrentes dessas ações.
A qualidade dos produtos, datas, prazos, bem como alterações de preços,
trocas e devoluções são de inteira responsabilidade dos vendedores,
cabendo ao Peça Agora apenas o acompanhamento dos processos e suporte
para eventuais situações.</p><p>
Os preços e prazos diferenciados decorrentes de promoções são de inteira
responsabilidade dos vendedores, cabendo a essas garantir os preços e
prazos anunciados.</p><p>
Durante o procedimento de venda dos produtos, o valor do frete poderá sofrer
alterações devido à localidade do endereço de entrega, bem como
atualização das tabelas pelas transportadoras. O Peça Agora não se
responsabiliza pelas alterações de frete devido a caso fortuito ou força maior.
Confira nossa Política de entregas.</p><p>
Desta mesma forma, variações no preço dos produtos, devido o possível
acréscimo de tarifas (fretes e impostos adicionais) poderão ocorrer durante o
processo de compras, sendo estas de inteira responsabilidade dos
vendedores no Peça Agora.</p><p>
O valor final e correto é o encontrado no carrinho no momento do pagamento,
onde todas as variações serão concluídas e consideradas pelos parceiros
para efetivação da compra e venda.</p><p>
O Peça Agora não se responsabiliza pelo direito de veiculação de publicidade
dos produtos e serviços anunciados, sendo os anunciantes inteiramente
responsáveis.</p><p>
A disponibilidade dos produtos expostos no site é de inteira responsabilidade
dos vendedores, cabendo ao Peça Agora apenas o acompanhamento e
controle das ações.</p><p>
Eventuais erros no sistema serão corrigidos pelo período necessário,
podendo ocasionar falhas operacionais e dificuldades de acesso. O Peça
Agora não se responsabiliza por danos decorrentes do erro de funcionamento
do sistema nessas situações.</p><p>
Os usuários são submetidos aos critérios de segurança explicitados na
política de privacidade apenas durante o acesso ao site. O Peça Agora não
se responsabiliza por conteúdos e informações pedidas em sites ou serviços
que sejam acessados através da plataforma, seja na figura de vendedores,
parceiros, patrocinadores, anunciantes e/ou relacionados. Dessa forma, ao
acessar esses sites e seus respectivos conteúdos o usuário será inteiramente
responsabilizado pelas consequências, quando houver.
O Peça Agora se reserva no direito de inabilitar, suspender, denunciar e/ou
aplicar quaisquer medidas jurídicas cabíveis aos usuários que utilizarem do
site para promover produtos, serviços e afins não relacionados ao Peça
Agora, bem como denegrir a imagem de outros usuários, vendedores,
anunciantes e o Peça Agora.</p><p>
O Peça Agora, os vendedores e os anunciantes poderão efetuar alterações
nos produtos e serviços prestados, e inclusive encerrar as atividades a
qualquer momento sem necessidade de enviar aviso prévio e sem a
necessidade de indenizar qualquer usuário, vendedor ou anunciante desde
que tenha cumprido as obrigações dos serviços já prestados.
Os usuários, vendedores e anunciantes poderão indenizar o Peça Agora em
razão do descumprimento do presente Termo e Condições de Uso e demais
políticas do site, bem como pela violação de qualquer lei e/ou direito de
terceiros.</p><p>
O site poderá sofrer atualizações constantes, com o objetivo de melhorar,
corrigir imprecisões, erros e falhas do sistema. O Peça Agora não se
responsabiliza pelas alterações das informações decorridas de atualizações,
sendo válidas como verdadeiras a última versão exposta no site.
O Peça Agora não se responsabiliza quanto à confidencialidade de
comentários e vídeos enviados e publicados, sendo o(s) autor(es) o(s)
único(s) responsável(eis).</p><p>
O Peça Agora não se responsabiliza por comentários de usuários nas redes
sociais, sendo os autores os únicos responsáveis.
</p>
<b>
12-	Proteção Intelectual:
</b>
<p>Os materiais do site Peça Agora, incluindo mas não se limitando a marca,
denominações, logotipo, apresentações, arquivos, vídeos ou qualquer outro
documento autorizado à utilização estão sob a proteção da legislação do
direito de propriedade intelectual.</p><p>
Os materiais do site Peça Agora poderão ser impressos e/ou utilizados
apenas para uso pessoal, estando proibido aos usuários, vendedores e
anunciantes na ausência de permissão do Peça Agora utilizar, copiar,
imprimir, transferir ou comercializar qualquer informação, imagem gráfica,
software, dados e relacionados, sendo os envolvidos passíveis de medidas
jurídicas cabíveis.</p><p>
Os usuários, vendedores e anunciantes detém o direito de propriedade sobre
textos, imagens, informações, listas e relacionados que publicarem no Peça
Agora. Porém, o site encontra-se no direito de publicar e distribuir o conteúdo
parcial ou integralmente, tanto na forma online quanto off-line.</p><p>
Ao publicar qualquer informação no site Peça Agora, os usuários, vendedores
e anunciantes asseguram que detém todos os direitos sobre a publicação dos
respectivos conteúdos. Estes cedem ao Peça Agora a permissão para usar,
copiar, modificar e apagar sem prejuízo aos usuários, vendedores e
anunciantes.</p><p>
Os usuários, vendedores e anunciantes podem remover e editar os
conteúdos de sua autoria expostos no site a qualquer momento. Porém, caso
o Peça Agora tenha publicado e/ou distribuído o material, este não se
responsabiliza a realizar as alterações posteriores.</p><p>
O Peça Agora não permite qualquer ação que viole os direitos autorais.
Dessa forma serão retirados, sem aviso prévio, qualquer conteúdo que
infrinja os direitos de propriedade intelectual a qualquer pessoa física ou
jurídica.</p>
<b>
13-	Foro de eleição:
</b>
<p>Para dirimir eventuais controvérsias referentes ao presente Termo e
Condições de Uso, fica instituído o foro da Comarca da cidade de Juiz de
Fora no estado de Minas Gerais.</p>
<b>
14-	Das Modificações e condições gerais:
</b>
<p>O presente documento poderá sofrer atualizações, sendo as alterações
válidas apenas quando estas se tornarem públicas. Toda e qualquer
alteração será informada nessa página, sendo válida apenas a última versão
do documento.</p>
<b>
15-	Aceitação do Termo de uso:
</b>
<p>O usuário, vendedores e anunciantes, ao utilizar o site declara ter lido,
entendido e concordado com o presente Termos e condições de uso e as
demais políticas Peça Agora.</p>
<b>
16-	Contato:
</b>
<p>Para mais dúvidas ou informações não explicitadas no presente documento,
o interessado poderá entrar em contato pelo telefone (32) 3015-0023 ou pelo
email sac@pecaagora.com.</p>
";

?>
<style type="text/css">

    p {
        text-align: justify;
        text-indent: 50px;
    }

</style>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-stacked col-md-3" role="tablist">
            <li role="presentation" class="active"><a href="#entrega" aria-controls="entrega" role="tab"
                                                      data-toggle="tab">Entregas</a></li>
            <li role="presentation"><a href="#pagamento" aria-controls="pagamento" role="tab" data-toggle="tab">Pagamento</a>
            </li>
            <li role="presentation"><a href="#privacidade" aria-controls="privacidade" role="tab" data-toggle="tab">Privacidade</a>
            </li>
            <li role="presentation"><a href="#termos" aria-controls="termos" role="tab" data-toggle="tab">Termos de
                    uso</a></li>
            <li role="presentation"><a href="#trocas" aria-controls="trocas" role="tab" data-toggle="tab">Trocas e
                    devoluções</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content col-md-9">
            <div role="tabpanel" class="tab-pane fade in active" id="entrega"><?= $entrega ?></div>
            <div role="tabpanel" class="tab-pane fade" id="trocas"><?= $trocas ?></div>
            <div role="tabpanel" class="tab-pane fade" id="pagamento"><?= $pagamento ?></div>
            <div role="tabpanel" class="tab-pane fade" id="privacidade"><?= $privacidade ?></div>
            <div role="tabpanel" class="tab-pane fade" id="termos"><?= $termos ?></div>
        </div>

    </div>


</div>
