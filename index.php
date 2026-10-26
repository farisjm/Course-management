
<?php
include "db.php";

$message = "";

// INSERT COURSE
if (isset($_POST['save'])) {
    $course = trim($_POST['course'] ?? '');
    $credit = filter_var(
        $_POST['credit'] ?? '',
        FILTER_VALIDATE_INT
    );

    if ($course !== "" && $credit !== false && $credit > 0) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO Course (course, credit) VALUES (?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "si", $course, $credit);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php");
        exit;
    } else {
        $message = "Please enter a valid course name and credits.";
    }
}

// DISPLAY COURSES
$result = mysqli_query(
    $conn,
    "SELECT * FROM Course ORDER BY id DESC"
);

$totalCourses = mysqli_num_rows($result);

$creditResult = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(credit), 0) AS total FROM Course"
);

$totalCredits = (int) mysqli_fetch_assoc($creditResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Course Management System</title>

    <style>
        :root {
            --navy: #10172a;
            --navy-light: #1c2942;
            --green: #10b981;
            --green-dark: #059669;
            --cyan: #22d3ee;
            --violet: #8b5cf6;
            --pink: #ec4899;
            --bg: #f3f6fc;
            --white: #ffffff;
            --text: #182338;
            --muted: #748096;
            --border: #e5eaf3;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 38px 22px;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(
                    circle at 5% 5%,
                    rgba(34, 211, 238, 0.12),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 95% 90%,
                    rgba(139, 92, 246, 0.10),
                    transparent 28%
                ),
                var(--bg);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Mouse-follow glow */
        .mouse-glow {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background: radial-gradient(
                400px circle at var(--mouse-x, 50%)
                var(--mouse-y, 50%),
                rgba(16, 185, 129, 0.09),
                transparent 75%
            );
        }

        .container {
            width: 100%;
            max-width: 1150px;
            margin: auto;
            animation: pageEnter 0.75s ease both;
        }

        /* Animated header */
        .hero {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 34px;
            margin-bottom: 26px;
            color: white;
            border-radius: 24px;
            background: linear-gradient(
                120deg,
                #10172a,
                #173d50,
                #075e54,
                #43318a
            );
            background-size: 300% 300%;
            animation: gradientMove 12s ease infinite,
                       pageEnter 0.7s ease both;
            box-shadow: 0 18px 45px rgba(16, 23, 42, 0.18);
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            right: -50px;
            top: -100px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 50%;
            box-shadow:
                0 0 0 25px rgba(255,255,255,.035),
                0 0 0 55px rgba(255,255,255,.025);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            border-radius: 18px;
            color: #064e3b;
            background: linear-gradient(
                135deg,
                #6ee7b7,
                #22d3ee
            );
            font-size: 29px;
            font-weight: 800;
            box-shadow: 0 8px 24px rgba(34, 211, 238, .2);
            animation: floatIcon 4s ease-in-out infinite;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(23px, 4vw, 34px);
            line-height: 1.25;
            letter-spacing: -.8px;
        }

        .hero p {
            margin: 7px 0 0;
            color: #c8d9eb;
            font-size: 14px;
        }

        .status {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 15px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 30px;
            background: rgba(255,255,255,.09);
            color: #e2fff2;
            font-size: 13px;
            white-space: nowrap;
            backdrop-filter: blur(10px);
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 12px #34d399;
            animation: pulse 2s infinite;
        }

        /* Statistics */
        .stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 26px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 25px;
            background: rgba(255,255,255,.88);
            border: 1px solid rgba(229,234,243,.9);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(24,35,56,.045);
            transition: transform .3s ease,
                        box-shadow .3s ease,
                        border-color .3s ease;
            animation: pageEnter .7s ease both;
        }

        .stat-card:nth-child(2) {
            animation-delay: .12s;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            border-color: #a7f3d0;
            box-shadow: 0 18px 35px rgba(16,185,129,.12);
        }

        .stat-icon {
            display: grid;
            place-items: center;
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            border-radius: 17px;
            font-size: 26px;
        }

        .stat-icon.green {
            color: #047857;
            background: #d1fae5;
        }

        .stat-icon.violet {
            color: #6d28d9;
            background: #ede9fe;
        }

        .stat-label {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .stat-number {
            margin: 2px 0 0;
            font-size: 30px;
            line-height: 1.3;
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* Main layout */
        .panel {
            margin-bottom: 26px;
            padding: 30px;
            background: rgba(255,255,255,.94);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 10px 35px rgba(24,35,56,.05);
            animation: pageEnter .8s ease both;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 24px;
        }

        .heading-icon {
            display: grid;
            place-items: center;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: linear-gradient(135deg, #d1fae5, #cffafe);
            font-size: 22px;
        }

        h2 {
            margin: 0;
            color: var(--navy);
            font-size: 22px;
            letter-spacing: -.4px;
        }

        .subtitle {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .field {
            min-width: 0;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #344258;
            font-size: 14px;
            font-weight: 700;
        }

        input[type="text"],
        input[type="number"] {
            display: block;
            width: 100%;
            min-height: 52px;
            padding: 13px 16px;
            border: 1px solid #dce3ef;
            border-radius: 12px;
            outline: none;
            color: var(--text);
            background: #f9fbff;
            font: inherit;
            transition: border-color .25s,
                        box-shadow .25s,
                        background .25s,
                        transform .25s;
        }

        input::placeholder {
            color: #a0aabd;
        }

        input:focus {
            border-color: var(--green);
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 0 0 4px rgba(16,185,129,.12),
                        0 5px 18px rgba(16,185,129,.07);
        }

        .form-actions {
            margin-top: 23px;
        }

        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border: 0;
            border-radius: 11px;
            color: white;
            background: linear-gradient(
                120deg,
                #059669,
                #10b981
            );
            box-shadow: 0 5px 14px rgba(16,185,129,.16);
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s ease,
                        box-shadow .2s ease,
                        filter .2s ease;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 22px rgba(16,185,129,.25);
            filter: brightness(1.04);
        }

        button:active {
            transform: scale(.97);
        }

        button.delete {
            padding: 8px 12px;
            color: #dc2626;
            background: #fff1f2;
            box-shadow: none;
        }

        button.delete:hover {
            color: white;
            background: #dc2626;
            box-shadow: 0 5px 14px rgba(220,38,38,.2);
        }

        .edit-link {
            display: inline-block;
            padding: 8px 12px;
            margin-right: 5px;
            border-radius: 9px;
            color: #6d28d9;
            background: #f3e8ff;
            font-size: 13px;
            font-weight: 700;
            transition: transform .2s, background .2s;
        }

        .edit-link:hover {
            color: #5b21b6;
            background: #e9d5ff;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Table */
        .table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .count-badge {
            padding: 7px 12px;
            border-radius: 30px;
            color: #047857;
            background: #d1fae5;
            font-size: 12px;
            font-weight: 800;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 15px;
        }

        table {
            width: 100%;
            min-width: 600px;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }

        thead th {
            padding: 16px 19px;
            color: #e5edf8;
            background: var(--navy);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        tbody td {
            padding: 17px 19px;
            border-bottom: 1px solid #edf0f6;
            font-size: 14px;
            transition: background .2s;
        }

        tbody tr {
            animation: rowEnter .45s ease both;
            transition: background .2s;
        }

        tbody tr:nth-child(2) { animation-delay: .04s; }
        tbody tr:nth-child(3) { animation-delay: .08s; }
        tbody tr:nth-child(4) { animation-delay: .12s; }
        tbody tr:nth-child(5) { animation-delay: .16s; }
        tbody tr:nth-child(6) { animation-delay: .20s; }
        tbody tr:nth-child(7) { animation-delay: .24s; }

        tbody tr:hover td {
            background: #f0fdf7;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .id-badge {
            display: inline-grid;
            place-items: center;
            min-width: 34px;
            height: 34px;
            padding: 0 7px;
            border-radius: 10px;
            color: #0f766e;
            background: #ccfbf1;
            font-weight: 800;
        }

        .course-name {
            font-weight: 700;
            color: #1e293b;
        }

        .credit-badge {
            display: inline-block;
            padding: 6px 11px;
            border: 1px solid #bbf7d0;
            border-radius: 30px;
            color: #15803d;
            background: #f0fdf4;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .actions form {
            margin: 0;
        }

        .error-message {
            margin: 0 0 18px;
            padding: 12px 15px;
            border: 1px solid #fecaca;
            border-radius: 11px;
            color: #b91c1c;
            background: #fef2f2;
            animation: pageEnter .3s ease both;
        }

        .empty-state {
            padding: 35px 15px;
            text-align: center;
            color: var(--muted);
        }

        .empty-icon {
            margin-bottom: 8px;
            font-size: 35px;
        }

        .footer {
            padding: 5px 10px 20px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        .footer strong {
            color: #047857;
        }

        /* Keyframes */
        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rowEnter {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(52,211,153,.5);
            }
            50% {
                opacity: .75;
                box-shadow: 0 0 0 6px rgba(52,211,153,0);
            }
        }

        /* Responsive */
        @media (max-width: 700px) {
            body {
                padding: 17px 12px;
            }

            .hero {
                align-items: flex-start;
                flex-direction: column;
                padding: 24px 20px;
                border-radius: 19px;
            }

            .brand-icon {
                width: 49px;
                height: 49px;
                border-radius: 14px;
                font-size: 24px;
            }

            .status {
                font-size: 12px;
            }

            .stats {
                gap: 12px;
            }

            .stat-card {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
                padding: 17px;
                border-radius: 16px;
            }

            .stat-icon {
                width: 43px;
                height: 43px;
                border-radius: 13px;
                font-size: 21px;
            }

            .stat-number {
                font-size: 25px;
            }

            .panel {
                padding: 20px 15px;
                border-radius: 17px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .table-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            h2 {
                font-size: 19px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }

            .mouse-glow {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="mouse-glow" id="mouseGlow"></div>

<main class="container">

    <!-- HEADER -->
    <header class="hero">
        <div class="hero-content brand">
            <div class="brand-icon">C</div>
            <div>
                <h1>Course Management</h1>
                <p>University Academic Portal · Manage your courses</p>
            </div>
        </div>

        <div class="status">
            <span class="status-dot"></span>
            System Online
        </div>
    </header>

    <!-- STATISTICS -->
    <section class="stats">

        <article class="stat-card">
            <div class="stat-icon green">▤</div>
            <div>
                <p class="stat-label">Total Courses</p>
                <p class="stat-number">
                    <?= $totalCourses ?>
                </p>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon violet">◈</div>
            <div>
                <p class="stat-label">Total Credits</p>
                <p class="stat-number">
                    <?= $totalCredits ?>
                </p>
            </div>
        </article>

    </section>

    <!-- ADD COURSE -->
    <section class="panel">

        <div class="panel-heading">
            <div class="heading-icon">✚</div>
            <div>
                <h2>Add New Course</h2>
                <p class="subtitle">
                    Enter the course details below
                </p>
            </div>
        </div>

        <?php if ($message !== "") { ?>
            <p class="error-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php } ?>

        <form method="POST" action="index.php">

            <div class="form-grid">

                <div class="field">
                    <label for="course">Course Name</label>
                    <input
                        type="text"
                        id="course"
                        name="course"
                        maxlength="100"
                        placeholder="e.g. Web Programming"
                        required
                    >
                </div>

                <div class="field">
                    <label for="credit">Number of Credits</label>
                    <input
                        type="number"
                        id="credit"
                        name="credit"
                        min="1"
                        step="1"
                        placeholder="e.g. 3"
                        required
                    >
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" name="save">
                    <span>＋</span> Save Course
                </button>
            </div>

        </form>

    </section>

    <!-- COURSE LIST -->
    <section class="panel">

        <div class="table-heading">
            <div class="panel-heading" style="margin:0">
                <div class="heading-icon">▦</div>
                <div>
                    <h2>Course List</h2>
                    <p class="subtitle">
                        View and manage registered courses
                    </p>
                </div>
            </div>

            <span class="count-badge">
                <?= $totalCourses ?> Courses
            </span>
        </div>

        <div class="table-wrap">
            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($totalCourses > 0) { ?>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>
                        <td>
                            <span class="id-badge">
                                <?= (int) $row['id'] ?>
                            </span>
                        </td>

                        <td class="course-name">
                            <?= htmlspecialchars($row['course']) ?>
                        </td>

                        <td>
                            <span class="credit-badge">
                                <?= (int) $row['credit'] ?> Credits
                            </span>
                        </td>

                        <td>
                            <div class="actions">

                                <a
                                    class="edit-link"
                                    href="edit.php?id=<?= (int) $row['id'] ?>"
                                >
                                    ✎ Edit
                                </a>

                                <form
                                    method="POST"
                                    action="delete.php"
                                    onsubmit="return confirm('Are you sure you want to delete this course?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $row['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete"
                                    >
                                        ✕ Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-icon">📚</div>
                                <strong>No courses yet</strong>
                                <div>Add your first course using the form above.</div>
                            </div>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>
        </div>

    </section>

    <footer class="footer">
        Built with <strong>PHP &amp; MySQL</strong>
        · Course Management System
    </footer>

</main>

<script>
    // Mouse-follow glow effect
    const mouseGlow = document.getElementById("mouseGlow");
    let mouseFrame = null;

    document.addEventListener("mousemove", function (event) {
        if (mouseFrame !== null) {
            cancelAnimationFrame(mouseFrame);
        }

        mouseFrame = requestAnimationFrame(function () {
            mouseGlow.style.setProperty(
                "--mouse-x",
                event.clientX + "px"
            );

            mouseGlow.style.setProperty(
                "--mouse-y",
                event.clientY + "px"
            );

            mouseFrame = null;
        });
    });

    // Prevent mouse glow from affecting touch devices
    if (window.matchMedia("(pointer: coarse)").matches) {
        mouseGlow.style.display = "none";
    }
</script>

</body>
</html>
