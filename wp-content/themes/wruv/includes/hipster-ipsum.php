<?php

function random_hipster_word() {
	global $hipster_words;
	return $hipster_words[ rand(0, count($hipster_words) ) ];
}

global $hipster_words;
$hipster_words = explode("\n",
"3 wolf moon
8-bit
90's
actually
aesthetic
American Apparel
art party
artisan
asymmetrical
Austin
authentic
banh mi
banjo
Banksy
beard
before they sold out
bespoke
bicycle rights
biodiesel
bitters
blog
Blue Bottle
Brooklyn
brunch
Bushwick
butcher
cardigan
Carles
chambray
chia
chillwave
church-key
cliche
cornhole
Cosby sweater
craft beer
cray
cred
crucifix
deep v
direct trade
disrupt
distillery
DIY
dreamcatcher
drinking vinegar
Echo Park
ennui
ethical
Etsy
fanny pack
fap
farm-to-table
fashion axe
fingerstache
fixie
flannel
flexitarian
food truck
forage
four loko
freegan
gastropub
gentrify
gluten-free
Godard
hashtag
heirloom
hella
Helvetica
High Life
hoodie
Intelligentsia
iPhone
irony
jean shorts
kale chips
keffiyeh
keytar
Kickstarter
kitsch
kogi
leggings
letterpress
literally
lo-fi
locavore
lomo
Marfa
master cleanse
McSweeney's
meggings
meh
messenger bag
mixtape
mlkshk
mumblecore
mustache
narwhal
Neutra
next level
normcore
occupy
Odd Future
organic
paleo
PBR
PBR&B
photo booth
pickled
Pinterest
Pitchfork
plaid
polaroid
pop-up
pork belly
Portland
post-ironic
pour-over
pug
put a bird on it
quinoa
raw denim
readymade
retro
roof party
salvia
sartorial
scenester
Schlitz
seitan
selfies
selvage
semiotics
shabby chic
Shoreditch
single-origin coffee
skateboard
slow-carb
small batch
squid
sriracha
street art
stumptown
sustainable
swag
synth
tattooed
Thundercats
tofu
Tonx
tote bag
tousled
Truffaut
trust fund
try-hard
Tumblr
twee
typewriter
ugh
umami
vegan
VHS
Vice
vinyl
viral
wayfarers
Wes Anderson
whatever
Williamsburg
wolf
XOXO
YOLO
you probably haven't heard of them
yr");