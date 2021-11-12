<?php


class JsonToPhpArrayWriter
{
    public function convert(string $filepath)
    {
        $jsonString = file_get_contents($filepath);
        if (!$jsonString) {
            throw new Horde_Exception("Could not find $filepath");
        }
        $data = json_decode($jsonString);
        if (!$data) {
            throw new Horde_Exception("$filepath does not contain valid json");
        }
        //TODO
    }
}
/*
def get_header(
    ns=None,
    cls_name="TranslationData",
    cls_const_name="DATA"
):
    if ns is None:
        ns = []
    ns = "\\".join(ns)
    return (
        "<?php\n"
        "declare(strict_types=1);\n"
        f"namespace {ns};\n\n"
        f"class {cls_name}\n"
        "{\n"
        f"    public const {cls_const_name} = [\n"
    )


def get_json_data(filepath):
    with open(filepath, 'r') as f:
        data = json.load(f)
    return data


def get_php_data_string(data):
    s = json.dumps(data, sort_keys=True, indent=4, ensure_ascii=False)
    s = re.sub("{\s", "[\n", s)
    s = re.sub("},?\n", "],\n", s)
    s = s.replace(": ", " => ")
    s = s.replace("\n", "\n    ")
    s = s[1:-1]
    s = s.strip("\n")
    return s


def get_footer():

    return "];\n}"


def main(cls_name="TranslationData"):
    s = get_header(["Horde", "Passwd"], cls_name)

    data = get_json_data("translation.json")
    s += get_php_data_string(data)

    s += get_footer()
    with open(cls_name + ".php", "w", encoding='utf-8') as f:
        f.write(s)


if __name__ == "__main__":
    main()
*/
