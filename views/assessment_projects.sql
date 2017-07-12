--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.3
-- Dumped by pg_dump version 9.6.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = public, pg_catalog;

--
-- Name: assessment_projects; Type: VIEW; Schema: public; Owner: hom
--

CREATE VIEW assessment_projects AS
 SELECT DISTINCT pm.prj_id,
    pm.milestone
   FROM ((assessment a
     JOIN prj_tutor pt ON ((a.prjtg_id = pt.prjtg_id)))
     JOIN prj_milestone pm ON ((pm.prjm_id = pt.prjtg_id)))
  ORDER BY pm.prj_id, pm.milestone;


ALTER TABLE assessment_projects OWNER TO hom;

--
-- Name: VIEW assessment_projects; Type: COMMENT; Schema: public; Owner: hom
--

COMMENT ON VIEW assessment_projects IS 'used by ipeer.php';


--
-- Name: assessment_projects; Type: ACL; Schema: public; Owner: hom
--

GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE assessment_projects TO peerweb;


--
-- PostgreSQL database dump complete
--

