<?php
    function resize($img, $nWidth=0, $nHeight=0,$q=100) {
        ###########################################################
        # CASO NÃO TENHA A EXTENSÃO INSTALADA
        ###########################################################
            if (!extension_loaded('gd') && !extension_loaded('gd2')) {
                trigger_error("GD is not loaded", E_USER_WARNING);
                return false;
            }

        ###########################################################
        # CAPTA OS DETALHES DA IMAGEM
        ###########################################################
            $imgInfo = getimagesize($img);
            switch ($imgInfo[2]) {
                case 1:
                    $im = imagecreatefromgif($img);
                break;
                case 2:
                    $im = imagecreatefromjpeg($img);
                break;
                case 3:
                    $im = imagecreatefrompng($img);
                break;
                default:
                    trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
            }

        ###################################################
        # SE NAO TIVER MEDIDAS, PEGA O TAMANHO ORIGINAL
        ###################################################
            if ($nWidth == 0 && $nHeight == 0) {
                $nWidth  = $imgInfo[0];
                $nHeight = $imgInfo[1];
            }


        ###################################################
        # INICIAMOS OS CALCULOS DE RESIZE & CROP
        ###################################################

            $largura_original = $imgInfo[0];
            $altura_original = $imgInfo[1];

            //Eixos iniciais
            $thumbX = 0;
            $thumbY = 0;

            //Largura e altura iniciais
            $thumbLargura = $largura_original;
            $thumbAltura = $altura_original;

            //Proporção inicial (paisagem, retrato ou quadrado)
            $proporcaoX = $largura_original / $nWidth;
            $proporcaoY = $altura_original / $nHeight;

            //Imagem paisagem
            if ($proporcaoX > $proporcaoY) {
                $thumbLargura   = round($largura_original / $proporcaoX * $proporcaoY);
                $thumbX         = round(($largura_original - ($largura_original / $proporcaoX * $proporcaoY)) / 2);
                
            //Imagem retrato
            }elseif($proporcaoY > $proporcaoX) {
                $thumbAltura = round($altura_original / $proporcaoY * $proporcaoX);
                $thumbY = round(($altura_original - ($altura_original / $proporcaoY * $proporcaoX)) / 2);
            }

        ###########################################################
        # CASO SEJA PNG TRANSPARENTE
        ###########################################################
            $newImg = imagecreatetruecolor($nWidth, $nHeight);
            if (($imgInfo[2] == 1) or ($imgInfo[2] == 3)) {
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
            }

        ###########################################################
        # INSERE O ARQUIVO NO QUADRO
        ###########################################################
            imagecopyresampled($newImg, $im, 0, 0, $thumbX, $thumbY, $nWidth, $nHeight, $thumbLargura, $thumbAltura);

        ###########################################################
        # MONTAMOS O HEADER PARA EXIBIÇAO DA IMAGEM
        ###########################################################
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
            clearstatcache();
            header("Content-type: " . $imgInfo['mime']);
            header("Content-Disposition: inline; filename = " . $newImg);


        ###################################################
        # SELECIONA A QUALIDADE DE EXIBIÇÃO
        ###################################################
        $extencao = substr($img, -3);
        $extensionList = array('jpg' => 100, 'png' => 9, 'gif' => 100);
        $q = (isset($_GET['q']) &&  $q<=$extensionList[$extencao] ) ? $_GET['q'] : $extensionList[$extencao];


        ###########################################################
        # VERIFICA QUAL É O MIME DA IMAGEM E RETORNA A VISUALIZAÇAO
        ###########################################################
            switch ($imgInfo[2]) {
                case 1:
                    imagegif($newImg, null, $q);
                break;
                case 2:
                    imagejpeg($newImg, null, $q);
                break;
                case 3:
                    imagepng($newImg, null, $q);
                break;
                default:
                    trigger_error('Failed resize image!', E_USER_WARNING);
                break;
            }
        ###########################################################
        # CASO QUEIRA SALVAR UMA NOVA IMAGEM 
        ###########################################################
             return $newfilename;
        
    }


###########################################################
# RETORNA A IMAGEM COM OS PARÂMETROS ENVIADOS VIA GET 
###########################################################
    resize($_GET['img'],$_GET['w'],$_GET['h'],$_GET['q']);
