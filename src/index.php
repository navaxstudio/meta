<?php

if(!file_exists(__DIR__."/test")) {
    copy(__DIR__ . "/Controller/MetaConfigController.php", dirname(__DIR__, 4) . "/src/Controller/" . "MetaConfigController.php");
    copy(__DIR__ . "/Entity/MetaConfigs.php", dirname(__DIR__, 4) . "/src/Entity/" . "MetaConfigs.php");
    copy(__DIR__ . "/Repository/MetaConfigsRepository.php", dirname(__DIR__, 4) . "/src/Repository/" . "MetaConfigsRepository.php");
    copy(__DIR__ . "/Config/Types.php", dirname(__DIR__, 4) . "/src/Config/" . "Types.php");
    copy(__DIR__ . "/Helper/MetaHelper.php", dirname(__DIR__, 4) . "/src/Helper/" . "MetaHelper.php");
    mkdir(__DIR__ . "/test");
}
