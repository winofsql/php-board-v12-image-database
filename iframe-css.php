<style>

/* IFRAME で表示する */
html,body {
    height: 100%;
}

body {
    margin: 0;
}
#bbs {
    width: 100%;
    /* height: <?= $view_head_height ?>px; */
    background-color: #fff;
}
#extend {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: calc( 100% - 4px );
    /* height: calc( 100% - <?= $view_head_height ?>px - 2px ); */
    border: solid 2px #c0c0c0;
}

/* PC 用 */
@media screen and ( min-width:480px ) {

#bbs {
    height: <?= $view_head_height ?>px;
}

#extend {
    height: calc( 100% - <?= $view_head_height ?>px - 2px );
}

}
/* スマホ 用 */
@media screen and ( max-width:479px ) {

#bbs {
    height: <?= $view_head_height + 40 ?>px;
}

#extend {
    height: calc( 100% - <?= $view_head_height - 40 ?>px - 2px );
}
}
</style>
