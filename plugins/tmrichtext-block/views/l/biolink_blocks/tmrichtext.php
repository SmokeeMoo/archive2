<?php defined('ALTUMCODE') || die() ?>
<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

<style>

@import url('https://fonts.googleapis.com/css2?family=Amatic+SC&family=Arsenal&family=Bad+Script&family=Caveat&family=Comfortaa:wght@300&family=Cormorant&family=Cormorant+Infant:wght@300&family=Cuprum&family=Exo+2:wght@300&family=Forum&family=Lobster&family=Marck+Script&family=Merriweather:wght@300&family=Oswald:wght@300&family=PT+Sans&family=Philosopher&family=Play&family=Poiret+One&family=Roboto+Condensed&family=Roboto+Slab:wght@300&family=Vollkorn&family=Yanone+Kaffeesatz:wght@300&display=swap');

    p {
      text-align: left;  
    }
    
    p.ql-align-right {
    text-align: right;
    }
    
    p.ql-align-left {
        text-align: left;
    }
    
    p.ql-align-center {
        text-align: center;
    }
    
    p.ql-align-justify {
        text-align: justify;
    }
    
    ol {
       
    }
    
    
    li, h1, h2, h3, h4, h5, h6 {
       text-align: left; 
    }
    
    
    li.ql-align-right, h1.ql-align-right, h2.ql-align-right, h3.ql-align-right, h4.ql-align-right, h5.ql-align-right, h6.ql-align-right {
        text-align: right;
    }
    
    ol li:before {
    content: counter(list-num, decimal) '. ';
    }
    
    ol > li, ul > li {
    list-style-type: none;
    }
    
/*li::before {
    display: inline-block;
    margin-right: 0.3em;
    text-align: right;
    white-space: nowrap;
    width: 1.2em;
    }
    
    ul > li::before {
    content: '\25CF';
    }*/

    ol li {
    counter-reset: list-1 list-2 list-3 list-4 list-5 list-6 list-7 list-8 list-9;
    counter-increment: list-num;
    }
    
    
    ul li.ql-align-right {
        text-align: right;
        float: none;
        list-style-type: none;
    }
    
    li.ql-align-center, h1.ql-align-center, h2.ql-align-center, h3.ql-align-center, h4.ql-align-center, h5.ql-align-center, h6.ql-align-center {
        text-align: center;
    }
    
    ul li.ql-align-center {
        text-align: center;
         display: block;
    }
    
    li.ql-align-justify, h1.ql-align-justify, h2.ql-align-justify, h3.ql-align-justify, h4.ql-align-justify, h5.ql-align-justify, h6.ql-align-justify {
        text-align: justify;
    }
    
    pre {
    text-align: left; 
    color: #F5F5F5;
    font-family: "Courier New",monospace;
    font-size: 80%;
    padding: 0 3px 2px;
    background-color: #333333;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 4px 4px 4px 4px;
    display: block;
    line-height: 20px;
    margin: 0 0 10px;
    padding: 9.5px;
    white-space: pre-wrap;
    word-break: break-all;
    word-wrap: break-word;
    }
    
    pre.ql-align-right {
        text-align: right;
    }
    
    pre.ql-align-center {
        text-align: center;
    }
    
    pre.ql-align-justify {
        text-align: justify;
    }


blockquote {
    font-size: 16px;
    font-style: italic;
    margin: 16px;
    padding: 16px 24px;
    position: relative;
}
blockquote:before {
    content: "";
    position: absolute;
    top: 50%;
    left: -6px;
    height: 40px;
    width: 6px;
    margin-top: -1em;
}
blockquote:after {
    content: "”";
    position: absolute;
    top: 50%;
    left: -20px;
    font-size: 50px;
    font-family: Times, sans-serif;
    font-weight: bold;
    line-height: 30px;    
}

.ql-size-large {
    font-size: 130%; 
}

.ql-size-huge {
   font-size: 180%;  
}

.ql-size-small {
  font-size: 80%; 
}

.ql-font-play {
    font-family: 'Play', sans-serif;
}

.ql-font-roboto {
    font-family: 'Roboto', sans-serif;
}

.ql-font-amatic-sc {
    font-family: 'Amatic SC', cursive;
}

.ql-font-arsenal {
    font-family: 'Arsenal', sans-serif;
}

.ql-font-bad-script {
    font-family: 'Bad Script', cursive;
}

.ql-font-caveat {
    font-family: 'Caveat', cursive;
}

.ql-font-comfortaa {
    font-family: 'Comfortaa', cursive;
}

.ql-font-cormorant {
    font-family: 'Cormorant', serif;
}

.ql-font-cormorant-infant {
    font-family: 'Cormorant Infant', serif;
}

.ql-font-cuprum {
    font-family: 'Cuprum', sans-serif;
}

.ql-font-exo-2 {
    font-family: 'Exo 2', sans-serif;
}

.ql-font-forum {
    font-family: 'Forum', cursive;
}

.ql-font-lobster {
    font-family: 'Lobster', cursive;
}

.ql-font-marck-script {
    font-family: 'Marck Script', cursive;
}

.ql-font-merriweather {
    font-family: 'Merriweather', serif;
}

.ql-font-oswald {
    font-family: 'Oswald', sans-serif;
}

.ql-font-philosopher {
    font-family: 'Philosopher', sans-serif;
}

.ql-font-poiret-one {
    font-family: 'Poiret One', cursive;
}

.ql-font-pt-sans {
    font-family: 'PT Sans', sans-serif;
}

.ql-font-roboto-condensed {
    font-family: 'Roboto Condensed', sans-serif;
}

.ql-font-roboto-slab {
    font-family: 'Roboto Slab', serif;
}

.ql-font-vollkorn {
    font-family: 'Vollkorn', serif;
}

.ql-font-yanone-kaffeesatz {
    font-family: 'Yanone Kaffeesatz', sans-serif;
}

strong {
    font-weight: 700;
}

</style>
 

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2" style="color: <?= $data->link->settings->text_color ?>">
           

    <?= $data->link->settings->text ?>



</div>

