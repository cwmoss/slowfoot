import Navigo from "./navigo.js";

const removePrefix = (value, prefix) =>
  value.startsWith(prefix) ? value.slice(prefix.length) : value;

/*
https://stackoverflow.com/questions/2136461/use-javascript-to-intercept-all-document-link-clicks

intercept all links
*/
function setup_linkhandler(router) {
  document.addEventListener("click", (e) => {
    console.log("$$$ click handler: target", e);

    // let target = e.originalTarget.closest("a") ?? null;
    let target = e.target.closest("a");
    if (target) {
      e.preventDefault(); // tell the browser not to respond to the link click
      router.navigate(target.getAttribute("href"));
    }
  });
}
/*
    root will be / when empty
    root will be anything/without/start-slash otherwise
*/
export default function NavigoPlus(base, options) {
  let router = new Navigo(base, options);
  setup_linkhandler(router);
  router.start_from_current_url = () => {
    let path = new URL(window.location.href).pathname;
    let root = router.root;
    // console.log("START", path, root);
    path = removePrefix(path, "/");
    root = removePrefix(root, "/");
    path = removePrefix(path, root);
    // console.log("START", path);
    return router.navigate(path);
  };
  return router;
}
