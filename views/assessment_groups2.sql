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
-- Name: assessment_groups2; Type: VIEW; Schema: public; Owner: hom
--

CREATE VIEW assessment_groups2 AS
 SELECT DISTINCT assessment.judge AS snummer,
    assessment.prjtg_id
   FROM assessment;


ALTER TABLE assessment_groups2 OWNER TO hom;

--
-- Name: VIEW assessment_groups2; Type: COMMENT; Schema: public; Owner: hom
--

COMMENT ON VIEW assessment_groups2 IS 'needed by student_project_attributes and student milestone selector';


--
-- Name: assessment_groups2; Type: ACL; Schema: public; Owner: hom
--

GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE assessment_groups2 TO peerweb;


--
-- PostgreSQL database dump complete
--

