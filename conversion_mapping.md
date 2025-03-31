# PHP Code Refactoring Mapping

This document maps the old PHP files to their new locations in the refactored MVC structure.

## Controllers
- `index.php` → `Controllers/HomeController.php`
- `login.php`, `logout.php` → `Controllers/AuthController.php`
- `show-mod.php`, `edit.php` (mod related) → `Controllers/ModController.php`

## Models
- `lib/user.php` → `Models/User.php`
- `lib/asset.php` (mod related) → `Models/Mod.php`

## Services
- `lib/assetcontroller.php`, `lib/asseteditor.php` → `Services/AssetService.php`
- `login.php`, `logout.php` (auth logic) → `Services/AuthService.php`
- `delete-comment.php`, `edit-comment.php` → `Services/CommentService.php`
- `lib/fileupload.php`, `edit-uploadfile.php`, `edit-deletefile.php` → `Services/FileService.php`
- `lib/assetimpl/modeditor.php`, `lib/assetimpl/modlist.php` → `Services/ModService.php`
- `lib/notification.php`, `notifications.php` → `Services/NotificationService.php`
- `lib/assetimpl/releaseeditor.php`, `lib/assetimpl/releaselist.php` → `Services/ReleaseService.php`
- `lib/tags.php`, `edit-tag.php` → `Services/TagService.php`
- `edit-profile.php`, `moderate-user.php` → `Services/UserService.php`

## Core Framework
- `lib/core.php` → `Core/App.php`, `Core/Controller.php`
- `lib/View.php` → `Core/View.php`
- `lib/ErrorHandler.php` → Error handling in `Core/App.php`
- `lib/config.php` → Configuration in `Core/App.php`

## Notes
- The new structure follows a proper MVC architecture
- Business logic has been moved from controllers to dedicated service classes
- Database operations are now handled through models
- Core functionality is properly separated into framework classes
- API endpoints will be handled by respective controllers with proper routing