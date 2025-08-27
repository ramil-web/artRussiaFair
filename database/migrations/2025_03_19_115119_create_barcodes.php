<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          $codes = array(
              array(
                  'barcode' => 320789857693,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 126321402496,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 713274530985,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 715600069068,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 177207136226,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 826811569488,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 814832197050,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 440784221366,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 141687035320,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 725272281584,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 605266635031,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 872892134145,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 737827436288,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 462968684977,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 256234690785,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 202017562931,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 632498081431,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 399582083616,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 299853458627,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 201253564191,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 125413194788,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 671845226056,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 836617144790,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 949395319306,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 709233366520,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 131152692931,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 209077469826,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 792374293126,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 273690943768,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 969035896257,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 148740137786,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 105016154428,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 856922296860,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 236654853296,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 484418143690,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 674327653250,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 252738721345,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 133363429115,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 559533263891,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 131137754704,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 656094567157,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 985833586459,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 326263641124,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 326783506038,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 672790447410,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 642243549267,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 447790012180,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 228094890833,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 103969419006,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 122841546426,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 798541213850,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 666985748118,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 986134892409,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 733967825522,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 170451256046,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 833140037984,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 795876020096,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 123660642768,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 386944634548,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 439278874303,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 964073503635,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 464163303155,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 442717830038,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 234445645133,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 870283389995,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 125650206511,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 155080016567,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 115945058982,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 107512574370,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 399509984999,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 823353148459,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 148396558545,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 709632879038,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 847732277631,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 220493966453,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 960071466732,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 357188772649,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 146220082553,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 263759217470,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 795720991767,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 945142489155,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 196494970840,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 327398646369,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 252396146256,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 949215564363,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 663807009406,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 869669702248,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 749737860867,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 118268639052,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 899739996995,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 930918794247,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 690073720542,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 333408144892,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 224367870878,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 662592845468,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 370229828795,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 271307740706,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 823055217064,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 796556060664,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 427344801861,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 450823292183,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 319815937541,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 619369786437,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 889959107236,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 135171562372,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 158904322757,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 502914673837,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 525516500906,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 458858055489,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 888931148954,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 560245933532,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 544568999339,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 704874188457,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 391765189442,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 199947506096,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 873392513649,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 195206807269,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 748673318092,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 968208381131,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 558175981471,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 791744820579,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 419844360575,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 451616068511,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 102382659355,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 881002513953,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 117880433721,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 610982217929,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 148014839969,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 113718600863,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 728796459481,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 723941925190,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 196005046084,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 708895151697,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 405239070304,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 119762573316,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 328613863073,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 566076841360,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 198211820980,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 671609629198,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 576953168750,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 817900146281,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 347800863392,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 547391690730,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 570510956151,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 513459553116,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 621409506650,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 185921251108,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 940094814021,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 788786809229,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 226372651966,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 602275707109,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 106161170179,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 696857472226,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 285118043501,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 245802179301,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 704455618005,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 572713731203,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 841736933627,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 938854760899,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 162884657236,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 745392242209,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 896767289654,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 930324926348,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 205544596543,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 332699599985,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 771667633644,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 667665242187,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 540907501560,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 468531952769,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 234216191372,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 433596663953,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 630667495569,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 780589043051,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 655460960655,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 628733467803,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 547063845160,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 235888450626,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 158582558258,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 490349154072,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 789667505854,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 390816827389,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 818788770510,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 539911796003,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 589501792275,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 441604492013,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 785245820586,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 345506765828,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 797692768273,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 146878307542,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 611729531653,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 301232936327,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 418136608151,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 633775730741,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 578786013535,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 893229283293,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 462558863556,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 449831460742,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 684044584896,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 148640569158,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 568046152748,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 555388382594,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 440240497365,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 366350609405,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 198773705213,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 879821517206,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 827922082811,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 412402521364,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 584625763431,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 696543415725,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 472969630708,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 317808844724,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 581917976198,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 496438548058,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 795347230132,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 243927042785,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 876765344549,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 663474764175,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 715409021187,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 321775127781,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 275322839967,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 635434412151,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 195624873969,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 798239498863,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 106203215481,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 233982207192,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 890109600117,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 806519140309,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 213153867622,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 291527824841,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 820920466185,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 874015036759,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 318278105574,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 481425277871,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 482853932197,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 641673813705,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 146393673444,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 506692074897,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 309882964855,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 427824260972,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 139990877583,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 909684928143,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 724372339271,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 976186695672,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 728472767144,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 355578917503,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 900021131057,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 700467835367,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 608975295265,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 866596694539,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 384836141657,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 312793043454,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 679819153509,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 401703128406,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 714263660939,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 355958004999,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 635105749345,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 163663795370,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 893936970731,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 212104888364,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 284836745154,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 703230765465,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 498130440244,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 235678078267,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 994986127684,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 683910324409,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 239697617422,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 899338414146,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 930427914793,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 784361256551,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 329289244142,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 897520932552,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 189571124026,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 924439566325,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 658488246759,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 691597194310,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 314229073854,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 946254257249,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 851045825845,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 953858978630,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 664184764180,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 235056314556,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 188683701761,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 569760105265,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 227565286986,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 496176108155,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 779535074885,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 845858052229,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 855763921193,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 447068635418,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 792705385007,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 738061199186,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 289090922278,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 350453333417,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 757071637229,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 830557709402,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 667228346380,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 502595799418,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 944541674335,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 323206220402,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 640319721438,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 157495326509,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 229810313535,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 449214621399,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 467061587578,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 631515174202,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 995510624438,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 931341899308,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 136177069727,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 205563833849,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 880395818390,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 777722647579,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 153444541295,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 816219059206,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 827437856073,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 675624335348,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 790198269756,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 733424710047,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 523561665903,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 247125864538,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 183613035905,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 464791504167,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 478797858840,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 414312383266,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 463031155302,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 969057104148,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 245081183915,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 835305159851,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 665343984743,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 824065874260,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 169432351057,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 602898404563,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 230455129994,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 352838431627,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 230110633773,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 896832759861,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 569103336212,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 941745152083,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 471504466488,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 683155097331,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 936683253698,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 548862547100,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 298515714424,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 732312252961,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 727567979253,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 933257931967,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 684065496906,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 798199656324,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 109480600812,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 396611111328,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 150562310731,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 191460504747,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 482965396421,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 853474592094,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 369605284807,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 841505685301,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 162437060254,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 713029579923,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 785905079064,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 915206885724,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 216061719987,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 868587311048,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 958880032840,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 201711427328,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 202873426492,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 339468860197,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 124324108124,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 807214915386,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 718443022033,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 785759782964,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 863725280821,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 539218891727,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 471908018246,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 213558923931,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 410723993285,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 182002159843,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 861801700258,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 336489469126,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 337361213879,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 481734444370,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 316980120928,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 387288901104,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 305944635445,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 327624566320,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 771641643877,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 512846739602,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 699589860345,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 376863292955,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 964370425850,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 631545057363,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 120298860054,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 902049427574,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 356525986228,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 355825492977,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 252227093328,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 690161939235,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 643309608328,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 911701707925,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 732822578430,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 918590221664,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 958087608551,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 736629170306,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 673442361665,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 566018941687,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 593655239877,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 285555685298,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 882937464567,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 206087261909,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 969497397979,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 565941807223,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 627694358236,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 932993144293,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 477897120246,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 683383588849,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 683490708734,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 236147769108,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 335667837258,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 752635588732,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 579342253393,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 734059118697,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 633319577512,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 826816507267,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 709914043275,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 347294859632,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 909652546336,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 958021246509,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 215807369459,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 578425403943,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 848092952601,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 708214066744,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 323507297526,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 372988655113,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 375495372224,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 577790779040,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 998843054565,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 878177628180,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 296064047139,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 121894844828,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 651702230285,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 685565374778,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 960698651020,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 583462599183,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 539778128117,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 804897644375,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 735328692882,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 627442968196,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 403734762597,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 224250378389,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 127420928820,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 703020844890,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 210884296345,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 679890629351,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 420239893365,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 265818912597,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 968676901858,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 112767524749,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 224767456319,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 855950550135,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 340429948494,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 664890672978,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 969329852954,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 133527752136,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 413335216802,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 551895952831,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 766282755384,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 992585379623,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 962854017189,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 661234512795,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 598046191221,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 912043620843,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 146689677840,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 678987894187,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 280839491803,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 625335899391,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 654103265008,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 300215543434,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 717442542982,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 929745028568,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 108449258756,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 993107262061,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 293271253158,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 234465054268,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 715671355478,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 387846868473,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 428324904505,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 547827972143,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 654083932986,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 497419580256,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 664493230790,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 748871765517,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 848401070524,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 980051053110,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 585636977186,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 606919691345,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 761017943696,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 697340310921,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 100581017044,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 935253128349,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 764856888041,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 515279320455,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 106871699082,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 293325001274,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 211606335278,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 880871961620,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 632386856090,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 287779213942,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 176599962894,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 665662972317,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 358473410791,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 220304656979,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 559804388284,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 753818067399,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 984580388837,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 322941384113,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 274939401424,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 417652772849,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 961224674117,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 804670455404,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 437993188424,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 480113084223,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 380486507230,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 908502482165,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 937445198872,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 135213041897,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 432317608997,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 197393796683,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 859900258816,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 761837350169,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 136083926666,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 929335057949,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 732887363837,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 861120994166,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 465589217437,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 496382786609,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 341246591453,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 616516760938,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 642875737550,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 266264767820,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 273425553434,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 198979207686,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 720051567927,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 790443524243,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 738239452092,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 912834474363,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 599976546876,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 584320724182,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 402581440380,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 190437452393,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 495355234849,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 598088993409,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 205770363943,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 227112577042,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 463482291124,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 662426940782,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 815047857551,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 824343429249,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 827371040570,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 901235510410,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 177411935457,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 459983782805,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 443261056992,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 582382640412,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 576197257709,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 506580735562,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 639535259119,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 792219906013,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 896637484986,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 406409663433,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 852322843160,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 736723651957,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 227120401549,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 585051364624,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 772168916717,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 806023738818,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 765207389896,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 485003361464,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 228473961566,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 676251619380,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 592791716934,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 877054650128,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 629189502847,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 461385305748,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 803891886401,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 221754692701,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 178588786515,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 697671312277,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 948732457448,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 947151483825,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 257562678454,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 669604139001,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 504691137750,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 562970969046,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 654504774934,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 269128605135,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 623177125978,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 118484550172,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 542888922284,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 348805174649,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 141574620587,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 750947822119,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 994113637780,
                  'productId' => 255525987
              ),
              array(
                  'barcode' => 229905321570,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 321454972607,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 638773188217,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 743872425466,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 584893992715,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 718410635197,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 886032737126,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 169202891429,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 840836905819,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 553351790507,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 246428470077,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 802251419706,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 225197346531,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 758763409553,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 690005265539,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 381505962071,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 738842492307,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 604730298002,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 440569834076,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 928530768238,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 877232692648,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 810920848987,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 812678499717,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 642880456562,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 100898780022,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 431429314176,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 589344551963,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 314959262511,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 280401622858,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 193274074507,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 248869412717,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 389328773868,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 957698591852,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 392590883181,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 740216056574,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 931063729087,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 328542946305,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 863019020821,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 940751481652,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 307383566822,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 573801037551,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 836044214885,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 720907347668,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 673118274540,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 501357690059,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 468008243076,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 480214010719,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 812268618785,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 411454263247,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 407490035432,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 639498906805,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 983345513216,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 428351192668,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 588839644555,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 233069115349,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 629032993436,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 954281439962,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 870327021153,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 647957045070,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 278517268764,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 960491303690,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 242955954040,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 172017996359,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 182014947274,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 510398633787,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 659716119300,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 941295165420,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 947439672097,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 629973883861,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 342823347302,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 744155246805,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 253816166383,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 325386542850,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 198275419509,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 739030687826,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 357380895928,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 639237435012,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 629359804680,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 404975976613,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 430581916338,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 636549223714,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 319474606327,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 241351109595,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 510676228171,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 130964951711,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 392524631780,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 379560041222,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 379195700007,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 788954531949,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 677651409784,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 375062601151,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 827603336635,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 749176588514,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 386201754851,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 895520648356,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 539353127907,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 588300197840,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 223802210599,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 957816823625,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 184576356303,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 386875959939,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 789732634918,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 623313026803,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 335630397811,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 667436919983,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 660904502180,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 250529864384,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 712203506484,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 377374446557,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 360559529029,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 154537635347,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 991455913140,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 147100234354,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 572077837281,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 945853401949,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 422790704316,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 720619367268,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 586928242060,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 966338896533,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 278361458404,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 492094243683,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 944814107143,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 322896445377,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 231982100691,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 801868507547,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 947939339976,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 294307494437,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 660664937329,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 704142962120,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 533008447133,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 490150439264,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 716058924622,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 594042217260,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 442548409144,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 462316421198,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 401352923271,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 165134354182,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 325653022831,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 901474848949,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 197421527372,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 138011982327,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 245710520675,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 926668654686,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 495586355771,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 176399470285,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 191589336275,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 952878759478,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 780669006407,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 357078889251,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 958754440917,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 102900976969,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 526605196973,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 179666552580,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 774494329071,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 298669881931,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 504555397857,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 198800224716,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 236482291702,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 304238122705,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 252460308887,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 258879367317,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 546841801839,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 460356531907,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 825474492484,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 341083172840,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 651586454409,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 610670681028,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 538741452655,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 240328074004,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 617836934996,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 976225701697,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 388032520085,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 586863169155,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 658384135978,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 427806664004,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 503145995087,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 109790533417,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 457982781956,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 813834659209,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 941290090178,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 860276383310,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 173917074104,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 362213560996,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 917080594801,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 264885855060,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 279382495749,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 324571326491,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 752689344391,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 180232207831,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 450484452071,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 137391321675,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 656351645995,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 534435981637,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 447395439955,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 190866127229,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 900174060557,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 685788608329,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 628693616044,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 873357832686,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 335832879446,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 820880818107,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 974516231822,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 228157644465,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 364625761252,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 374747266410,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 538842813334,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 165564658460,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 496419793550,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 242170754114,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 533742789024,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 461852807236,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 371794839084,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 607703280269,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 900485376176,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 540169227719,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 259123543891,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 762100887263,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 648081565786,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 454918367373,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 310991490828,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 314849622189,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 383496006935,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 553054134876,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 256841103040,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 802770346685,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 541343262457,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 160865469333,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 984631158024,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 860479381271,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 687704424734,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 722346241327,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 935515365410,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 573952373651,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 185733413496,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 563640096377,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 337837712502,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 146432272946,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 241950376277,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 979443295560,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 932145831950,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 396057749815,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 536061361745,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 120922727622,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 273256171935,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 303839320972,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 613918153861,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 830167637421,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 148198329050,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 344536117971,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 512677882809,
                  'productId' => 264394849
              ),
              array(
                  'barcode' => 340331314448,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 152470632039,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 234236781517,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 878539763277,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 584401071429,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 197039090466,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 484409678806,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 230708685918,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 624054526001,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 380934423563,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 721539916490,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 681125204061,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 647796231552,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 478278165756,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 257395668195,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 786290806705,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 923240916452,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 565236755055,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 730606700787,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 294673122274,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 723605618927,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 996725261953,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 125606009574,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 416263427134,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 778827632198,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 751630770370,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 471812550045,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 200038120336,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 654687938797,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 839004356916,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 749167935037,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 283428156267,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 242900156549,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 664526170831,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 540172439664,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 682817418950,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 612255605880,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 687776343978,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 657726685477,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 707096531062,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 815570523697,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 832247773494,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 972849585928,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 994188773999,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 934217035429,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 302701309977,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 143648173514,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 205280109779,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 807765126980,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 400293027423,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 935957981027,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 380840995517,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 412633688387,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 512747722503,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 628780134887,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 732369663130,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 356278988305,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 778250412436,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 280523544345,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 625970233442,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 368999139097,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 877528144677,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 852319099802,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 696586196959,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 464542446811,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 257307319582,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 708233653575,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 340950144492,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 904226465992,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 412028156232,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 890773377279,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 416717365027,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 700946142804,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 867797030851,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 279904903732,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 408296496056,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 338765894285,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 562160129716,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 432426018856,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 456929793634,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 690108512146,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 399970190879,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 188733620186,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 586483668430,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 144863721304,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 474334291717,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 408854653694,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 799506397544,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 111206272906,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 817638210130,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 134546139123,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 497909354975,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 649639095225,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 862235217320,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 635993874851,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 924043269105,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 333153842141,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 113810181537,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 764285472747,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 297408951164,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 272477420207,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 600667026267,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 160653320011,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 446686446617,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 541439208427,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 867443543362,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 208751050856,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 159554678816,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 453056042597,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 872349302817,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 497408972957,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 799903363641,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 121759755370,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 814581682081,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 881490764004,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 817365980164,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 796813349888,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 483436429205,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 714248608717,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 720072305593,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 553412930624,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 634471534317,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 674519379227,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 505900999257,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 141721175649,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 539869946837,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 982315011864,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 205432306700,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 162655323336,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 141836985891,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 431281424715,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 218090799374,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 562468968483,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 124369809613,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 301082269103,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 101511429251,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 819253336656,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 252487136006,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 324252157029,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 188683362294,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 766991073141,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 974956731847,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 521534849630,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 234828159152,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 368197980027,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 582584790962,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 855605810839,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 513523890090,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 656778216973,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 757347714488,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 278879225327,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 282852453629,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 264560193814,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 481248202623,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 262950708310,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 365106237088,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 770738732694,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 558356390202,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 372399422407,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 850658116723,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 911186018876,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 756887910940,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 880472740792,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 817267479391,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 804104044106,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 922846126298,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 697416605518,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 345576970973,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 689489601635,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 690386295123,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 412209795426,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 718786059803,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 547323580223,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 130350088822,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 251016068412,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 196431103252,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 173322948881,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 825532702287,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 125491406326,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 754819002426,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 212414991960,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 842790428216,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 301559811273,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 744799163047,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 337422399257,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 508109721269,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 417559713607,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 460923669916,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 199788908753,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 347952839869,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 113280381554,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 821028293502,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 180290745366,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 236546123248,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 672036236219,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 740049981735,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 312900129810,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 284929233589,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 197830872701,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 294700081827,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 319100303220,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 810881627547,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 535124797220,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 169713102066,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 408702367086,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 738203658851,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 347824817195,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 465115199857,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 107042045339,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 248038665529,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 849632839811,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 110952569461,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 961679864266,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 473620898717,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 343954900041,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 472831615128,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 388483189873,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 725972674252,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 697408704736,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 217287924529,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 352068826232,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 233992980452,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 477523795308,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 767291889588,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 681108347215,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 235708193607,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 848757156402,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 305288831987,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 861950002947,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 329727170923,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 407542823820,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 476045717090,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 677259813134,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 483801598552,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 697168657087,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 192266700502,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 478825998565,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 867570271033,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 374677765345,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 711021705075,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 837589111121,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 815274368427,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 152187602829,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 454371740719,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 563651251014,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 420304705594,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 513204749965,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 768424501799,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 681499387306,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 932944445435,
                  'productId' => 264395571
              ),
              array(
                  'barcode' => 692726580326,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 565581620080,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 111788161389,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 534017088482,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 840507831462,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 963771604247,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 847366600341,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 926297571069,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 473631050040,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 762588413956,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 626777017607,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 345744211735,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 628423467305,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 328624575984,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 888332340762,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 320969893430,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 403713511120,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 782104273216,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 326836193843,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 471225826881,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 212041568953,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 688033970993,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 664547931090,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 734381554587,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 872594760146,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 570143054304,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 579641793276,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 882183186320,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 644981003408,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 515266990675,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 285520394971,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 787761191174,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 798055680373,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 973483809338,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 413967856870,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 704311599307,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 556486277800,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 134711487251,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 951418360370,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 964822064586,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 669130586617,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 230359167261,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 254925501211,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 970628620470,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 747890880841,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 474545754561,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 385628011902,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 656297037896,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 799268215707,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 479699360187,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 821150505003,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 484841956184,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 521919225269,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 353164277275,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 313124843605,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 171170135840,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 534385591311,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 313071615167,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 439166969190,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 226804480912,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 657059017435,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 343157605867,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 506739957357,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 939917339794,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 398046442678,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 903902955543,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 392014774020,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 913430709311,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 727327113530,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 598615376091,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 928024726759,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 845208026417,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 707006563904,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 926529079234,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 941270033122,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 945645519016,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 296111166007,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 415579085811,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 331278739153,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 254360874420,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 251624564408,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 191895456790,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 281669202150,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 124932332546,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 738968101832,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 516279544696,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 258875035549,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 672386375975,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 845742426389,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 817905320430,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 228795853470,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 886249934869,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 179862684085,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 541418186613,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 568457991693,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 428214765459,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 120235221293,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 730812918237,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 903605057675,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 391456434495,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 305503339139,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 581732135994,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 803657134439,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 934172933207,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 116648123801,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 309034556158,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 222100055610,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 438866653981,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 739842527955,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 307484748128,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 483904065642,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 827091116855,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 459871232286,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 194009964535,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 347624838396,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 359058851524,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 993771236197,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 750400426844,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 113943587070,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 111600915539,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 295201714425,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 804700937033,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 332778976608,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 580768725071,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 839871589690,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 730918281274,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 128879639812,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 470725227772,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 584139172159,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 262393823883,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 528506707147,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 180359623656,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 970549087944,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 417472660837,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 597315367215,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 992014632366,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 972716312830,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 853754855276,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 967172037593,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 992751619585,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 758009036591,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 567683721769,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 764185987100,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 910656861784,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 125083092809,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 580943610128,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 212457359966,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 908473951004,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 249165432200,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 658154255578,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 162718568986,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 168541113351,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 838090635592,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 607128658897,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 692905401525,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 601321301703,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 589232721449,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 783806595004,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 889507414856,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 346658130033,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 981214339734,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 923425887453,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 714439406818,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 863792345266,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 982376501506,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 791299024030,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 993569545814,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 256406757008,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 529879073640,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 760318188891,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 577410528974,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 628212953292,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 839627954587,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 299291160329,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 730157057782,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 979640257712,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 942027979290,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 682045889908,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 780896697454,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 398900845273,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 351322385902,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 785294327498,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 858967352154,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 853007346254,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 176710012254,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 580700719338,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 143200159951,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 294773167832,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 912165352061,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 878459781481,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 581524282397,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 343099935020,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 785456826617,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 236056893352,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 316246136945,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 884441164587,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 258443497455,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 850732573169,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 738204015920,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 969559746766,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 578075648078,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 557676195407,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 693443482828,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 556617017888,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 335008597220,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 455886788997,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 247048218778,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 291211658614,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 909864147482,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 666790477434,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 280518346727,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 329010893711,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 826818462765,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 748729351953,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 784768125028,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 672306810759,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 611192881978,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 162180063558,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 180961872619,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 978931610546,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 971320349603,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 944978750352,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 712989713077,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 942272398101,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 986544749035,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 264175251558,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 740703641102,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 996068519722,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 259245707615,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 261328369995,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 832304264169,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 862102292070,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 689481774614,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 241509691850,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 683624776437,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 415560128461,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 643296055629,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 982860503609,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 484961670716,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 768803813931,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 742894031054,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 796028867453,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 938778804930,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 972814089407,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 334735057162,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 304915884234,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 781062888802,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 696228531930,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 699052710271,
                  'productId' => 264395777
              ),
              array(
                  'barcode' => 416221310585,
                  'productId' => 264395777
              )
          );
          $data =  [];
          DB::table('broadcasts')->where('codes_2025', true)->delete();
          foreach ($codes as $code) {
              $data[] = [
                  'barcode'    => $code['barcode'],
                  'product_id' => $code['productId'],
                  'codes_2025' => true,
                  'created_at' => now()
              ];
          }
          DB::table('broadcasts')
              ->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            //
        });
    }
};
