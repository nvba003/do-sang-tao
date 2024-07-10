<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Globe with Data Flow</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }
        svg {
            display: block;
            width: 100vw;
            height: 100vh;
        }
        .land {
            fill: #69b3a2;
            stroke: none;
        }
        .country {
            stroke: none;
        }
        .graticule {
            fill: none;
            stroke: #777;
            stroke-width: .5px;
            stroke-opacity: .5;
        }
        .arrow {
            stroke: blue;
            fill: none;
            opacity: 0.5;
        }
        .data-stream {
            font: 10px sans-serif;
            fill: white;
            opacity: 0.7;
        }
        .programmer {
            width: 40px;
            height: 40px;
        }
        .code-box {
            fill: black;
            stroke: none;
        }
    </style>
</head>
<body>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="https://unpkg.com/topojson@3"></script>
    <script>
        const width = window.innerWidth;
        const height = window.innerHeight;
        const svg = d3.select("body").append("svg")
            .attr("width", width)
            .attr("height", height);

        const projection = d3.geoOrthographic()
            .scale(300)
            .translate([width / 2, height / 2])
            .rotate([-65, -25])
            .clipAngle(90);

        const path = d3.geoPath().projection(projection);

        const graticule = d3.geoGraticule();

        const g = svg.append("g");

        d3.json("https://unpkg.com/world-atlas@1/world/110m.json").then(world => {
            const countries = topojson.feature(world, world.objects.countries).features;

            // Add ocean background
            svg.append("path")
                .datum({type: "Sphere"})
                .attr("d", path)
                .attr("fill", "#1da2d8");  // Ocean color

            svg.append("path")
                .datum(graticule)
                .attr("class", "graticule")
                .attr("d", path);

            svg.append("path")
                .datum(topojson.feature(world, world.objects.land))
                .attr("class", "land")
                .attr("d", path);

            svg.selectAll(".country")
                .data(countries)
                .enter().append("path")
                .attr("class", "country")
                .attr("d", path)
                .attr("fill", function(d) {
                    if (d.id === "756") return "#b712e0";  // Switzerland color
                    if (d.id === "704") return "#9b12e0";  // Vietnam color
                    return "#ccc";  // Default color for other countries
                });

            // Add programmer icons as SVG
            const programmerIcon1 = `
                <svg class="programmer" fill="#330eed" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 249" enable-background="new 0 0 256 249" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path d="M47.522,170.287l11.172,76.681h138.612l11.172-76.681H47.522z M128,215.249c-5.748,0-10.407-3.063-10.407-6.84 s4.659-6.84,10.407-6.84c5.748,0,10.407,3.062,10.407,6.84S133.748,215.249,128,215.249z M233.315,233.054h-26.028l10.292-70.639 H38.421l10.292,70.639H22.685c-14.848,0-24.85-15.195-18.978-28.834l36.809-85.489c7.149-16.605,23.493-27.362,41.571-27.362h14.486 l22.885,34.061l4.665-22.254l-7.809-11.807h23.491l-7.81,11.807l4.655,22.098l22.78-33.905h14.486 c18.078,0,34.422,10.757,41.571,27.362l36.808,85.489C258.165,217.858,248.164,233.054,233.315,233.054z M128,2.033 c22.496,0,40.733,18.237,40.733,40.733S150.496,83.498,128,83.498S87.267,65.261,87.267,42.765S105.504,2.033,128,2.033z"></path>
                    </g>
                </svg>
            `;
            const programmerIcon2 = `
                <svg class="programmer" fill="#330eed" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 249" enable-background="new 0 0 256 249" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path d="M47.522,170.287l11.172,76.681h138.612l11.172-76.681H47.522z M128,215.249c-5.748,0-10.407-3.063-10.407-6.84 s4.659-6.84,10.407-6.84c5.748,0,10.407,3.062,10.407,6.84S133.748,215.249,128,215.249z M233.315,233.054h-26.028l10.292-70.639 H38.421l10.292,70.639H22.685c-14.848,0-24.85-15.195-18.978-28.834l36.809-85.489c7.149-16.605,23.493-27.362,41.571-27.362h14.486 l22.885,34.061l4.665-22.254l-7.809-11.807h23.491l-7.81,11.807l4.655,22.098l22.78-33.905h14.486 c18.078,0,34.422,10.757,41.571,27.362l36.808,85.489C258.165,217.858,248.164,233.054,233.315,233.054z M128,2.033 c22.496,0,40.733,18.237,40.733,40.733S150.496,83.498,128,83.498S87.267,65.261,87.267,42.765S105.504,2.033,128,2.033z"></path>
                    </g>
                </svg>
            `;

            const icon1 = new DOMParser().parseFromString(programmerIcon1, 'text/html').body.firstChild;
            const icon2 = new DOMParser().parseFromString(programmerIcon2, 'text/html').body.firstChild;

            svg.node().appendChild(icon1);
            svg.node().appendChild(icon2);

            d3.select(icon1)
                .attr("x", projection([8.2275, 46.8182])[0] - 20)
                .attr("y", projection([8.2275, 46.8182])[1] - 20)
                .attr("width", 40)
                .attr("height", 40);

            d3.select(icon2)
                .attr("x", projection([108.2772, 14.0583])[0] - 20)
                .attr("y", projection([108.2772, 14.0583])[1] - 20)
                .attr("width", 40)
                .attr("height", 40);

            startAnimation();
        });

        // Coordinates for Switzerland and Vietnam
        const swissCoords = [8.2275, 46.8182];
        const vietnamCoords = [108.2772, 14.0583];

        const interpolate = d3.geoInterpolate(swissCoords, vietnamCoords);

        function startAnimation() {
            let t = 0;
            const dataStrings = ["0000", "0110", "1000", "1011", "1000", "1101", "1110", "0010", "1110", "1100"];

            function animate() {
                t += 0.006;
                if (t > 1) t = 0;

                const currentPosition = interpolate(t);
                const returnPosition = interpolate(1 - t);

                svg.selectAll(".arrow").remove();
                svg.selectAll(".data-stream").remove();
                svg.selectAll(".code-box").remove();

                const forwardOpacity = t > 0.9 ? (1 - t) / 0.1 : t < 0.1 ? t / 0.1 : 1;
                const returnOpacity = (1 - t) > 0.9 ? t / 0.1 : (1 - t) < 0.1 ? (1 - t) / 0.1 : 1;

                // Forward arrow
                svg.append("path")
                    .attr("class", "arrow")
                    .attr("stroke-width", 10 + 10 * t)  // Adjust the thickness of the arrow
                    .attr("d", path({
                        type: "LineString",
                        coordinates: [swissCoords, currentPosition]
                    }))
                    .style("opacity", forwardOpacity - 0.4);

                // Return arrow
                svg.append("path")
                    .attr("class", "arrow")
                    .attr("stroke-width", 10 + 10 * (1 - t))  // Adjust the thickness of the arrow
                    .attr("d", path({
                        type: "LineString",
                        coordinates: [vietnamCoords, returnPosition]
                    }))
                    .style("opacity", returnOpacity - 0.4);

                // Add forward code box
                const codeBoxSVG = `
                    <svg class="code-box" width="50" height="50" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-258.000000, -673.000000)" fill="#7912e0">
                                    <path d="M258,701 C258,703.209 259.791,705 262,705 L286,705 C288.209,705 290,703.209 290,701 L290,683 L258,683 L258,701 L258,701 Z M271,679 C270.448,679 270,678.553 270,678 C270,677.448 270.448,677 271,677 C271.552,677 272,677.448 272,678 C272,678.553 271.552,679 271,679 L271,679 Z M267,679 C266.448,679 266,678.553 266,678 C266,677.448 266.448,677 267,677 C267.552,677 268,677.448 268,678 C268,678.553 267.552,679 267,679 L267,679 Z M263,679 C262.448,679 262,678.553 262,678 C262,677.448 262.448,677 263,677 C263.552,677 264,677.448 264,678 C264,678.553 263.552,679 263,679 L263,679 Z M286,673 L262,673 C259.791,673 258,674.791 258,677 L258,681 L290,681 L290,677 C290,674.791 288.209,673 286,673 L286,673 Z" id="browser" sketch:type="MSShapeGroup"> </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                `;

                const forwardCodeBox = new DOMParser().parseFromString(codeBoxSVG, 'text/html').body.firstChild;
                svg.node().appendChild(forwardCodeBox);

                const forwardCodeBoxPosition = interpolate(t);
                d3.select(forwardCodeBox)
                    .attr("x", projection(forwardCodeBoxPosition)[0] - 50)
                    .attr("y", projection(forwardCodeBoxPosition)[1] + 15)
                    .style("opacity", forwardOpacity);

                const forwardDataStreamText = d3.select(forwardCodeBox).append("text")
                    .attr("class", "data-stream")
                    .attr("x", 5)
                    .attr("y", 25)  // Adjust y position to fit within the box
                    .text(dataStrings[Math.floor(t * dataStrings.length)]);

                // Add return code box
                const returnCodeBox = new DOMParser().parseFromString(codeBoxSVG, 'text/html').body.firstChild;
                svg.node().appendChild(returnCodeBox);

                const returnCodeBoxPosition = interpolate(1 - t);
                // const returnOpacity = (1 - t) > 0.8 ? t / 0.2 : 1;
                d3.select(returnCodeBox)
                    .attr("x", projection(returnCodeBoxPosition)[0] + 5)
                    .attr("y", projection(returnCodeBoxPosition)[1] - 65)
                    .style("opacity", returnOpacity);

                const returnDataStreamText = d3.select(returnCodeBox).append("text")
                    .attr("class", "data-stream")
                    .attr("x", 5)
                    .attr("y", 25)  // Adjust y position to fit within the box
                    .text(dataStrings[Math.floor((1 - t) * dataStrings.length)]);

                requestAnimationFrame(animate);
            }

            animate();
        }


    </script>
</body>
</html>
