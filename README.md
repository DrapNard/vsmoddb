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

Example: http://mods.vintagestory.at/api/tags

### /api/gameversions
List all game version tags

Example: http://mods.vintagestory.at/api/gameversions

### /api/mods
List all mods

Example: http://mods.vintagestory.at/api/mods

Get Parameters:
**tagids[]**: Search by tag id

**text**: Search by mod text and title


Search Example: http://mods.vintagestory.at/api/mods?text=jack&tagids[]=7&tagids[]=8


### /api/mod/[modid]
List all info for given mod

Example: http://mods.vintagestory.at/api/mod/6

