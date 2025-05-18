import yaml

# List of tables/views to exclude (case-insensitive, as they appear in the OpenAPI spec)
EXCLUDE = {
    "tjapcities", "tjapnames", "tnames", "tnames_hebr",
    "trecordger", "tcup", "tlink", "tmergelog",
    "tstat", "tstat0", "tstat2", "tstat3", "tstatlong",
    "tevent_submit", "first_performances",
    "unified_runners", "unified_runners_in_specified_events"
}

def should_exclude(name):
    # OpenAPI schemas/paths may use PascalCase, snake_case, etc.
    # We'll check lowercased and stripped of common prefixes
    name = name.lower()
    for ex in EXCLUDE:
        if ex in name:
            return True
    return False

with open("duv-openapi.yaml", "r", encoding="utf-8") as f:
    spec = yaml.safe_load(f)

# Remove excluded schemas/components
schemas = spec.get("components", {}).get("schemas", {})
for key in list(schemas.keys()):
    if should_exclude(key):
        del schemas[key]

# Remove excluded paths
paths = spec.get("paths", {})
for path in list(paths.keys()):
    if any(should_exclude(seg) for seg in path.split("/")):
        del paths[path]

# Remove auto-generated 'where.*' query parameters
for path_name, methods in spec.get("paths", {}).items():
    for operation_name, operation in methods.items():
        params = operation.get("parameters")
        if isinstance(params, list):
            operation["parameters"] = [
                p for p in params
                if not (p.get("in") == "query" and p.get("name", "").startswith("where."))
            ]

# Remove component-level parameters named 'where.*'
param_defs = spec.get("components", {}).get("parameters", {})
for pname in list(param_defs.keys()):
    if pname.startswith("where."):
        del param_defs[pname]

# Save the filtered spec
with open("duv-api-public.yaml", "w", encoding="utf-8") as f:
    yaml.dump(spec, f, allow_unicode=True, sort_keys=False)

print("Filtered OpenAPI spec written to duv-api-public.yaml")
