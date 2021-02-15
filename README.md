# vsmoddb
Repository for https://mods.vintagestory.at



# VS Mod DB API Docs

## URLS

*Api url*
http://mods.vintagestory.at/api

*Base url for all returned files*
http://mods.vintagestory.at/files/


## Interfaces

### /api/tags
List all mod tags

### /api/gameversions
List all game version tags

### /api/mods
List all mods

Get Parameters:
**tagids[]**: Search by tag id
**text**: Search by mod text and title

### /api/mod/[modid]
List all info for given mod


