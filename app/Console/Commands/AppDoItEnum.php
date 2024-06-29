<?php
    namespace App\Console\Commands;
    final class AppDoItEnum {
        public static $MODULES                = ["site", "panel", "dashboard", "api"];
        public static $ELEMENTS               = ["GetBtn", "GetFrm", "GetMdl", "GetMdlBtn", "GetAllBtn", "GetAllFrm", "GetAllMdl", "GetAllMdlBtn", "CreateBtn", "CreateFrm", "CreateMdl", "CreateMdlBtn", "UpdateBtn", "UpdateFrm", "UpdateMdl", "UpdateMdlBtn", "DeleteBtn", "DeleteFrm", "DeleteMdl", "DeleteMdlBtn"];
        public static $AUTHORIZATIONS         = ["login", "register", "lost-password", "logout"];
        public static $AUTHORIZATIONSELEMENTS = ["Btn", "Frm", "Mdl", "MdlBtn"];
    }
